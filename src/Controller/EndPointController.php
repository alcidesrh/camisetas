<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\ProductoVenta;
use App\Entity\TallaVenta;
use App\Entity\Venta;
use App\Utils\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/endpoint")
 * @author Alcides Rodríguez <alcdesrh@gmail.com>
 */
class EndPointController extends AbstractController
{
    /**
     * @Route(
     *     name="check_stock",
     *     path="/stock",
     *     methods={"GET"}
     * )
     */
    public function checkStock()
    {
        return new JsonResponse([$this->getUser()->getStock()]);

    }

    /**
     * @Route(
     *     name="get_ventas",
     *     path="/ventas",
     *     methods={"GET"}
     * )
     */
    public function getVentas(EntityManagerInterface $entityManager)
    {
        $response = [];
        foreach ($this->getUser()->getVentas() as $venta){
            $response[] = $venta;
        }

        return new JsonResponse($response);
    }

    /**
     * @Route(
     *     name="get_feria",
     *     path="/feria",
     *     methods={"GET"}
     * )
     */
    public function getFeria(EntityManagerInterface $entityManager)
    {
        $response = [];
        foreach ($this->getUser()->getVentas() as $venta){
            $response[] = ['id'=> $venta->getId(), 'name' => $venta->getFeria(), 'active' => $venta->getOpen()];
        }

        return new JsonResponse($response);
    }

     /**
     * @Route(
     *     name="feria_data",
     *     path="/feria/{id}",
     *     methods={"GET"}
     * )
     */
    public function getFeriaData(EntityManagerInterface $entityManager, $id)
    {
         return new JsonResponse($entityManager->getRepository('App:Venta')->find($id));
    }

    /**
     * @Route(
     *     name="check_feria",
     *     path="/feria",
     *     methods={"POST"}
     * )
     */
    public function checkFeria(EntityManagerInterface $entityManager, Request $request)
    {
        if($stock = $this->getUser()->getStock()){

            if ($data = Util::decodeBody()) {

                if (!$entityManager->getRepository('App:Venta')->findBy(
                        ['open' => true, 'user' => $this->getUser()]
                    ) && (isset($data['name']) || $request->get('name'))) {
                    $venta = new Venta();
                    $venta->setUser($this->getUser());
                    $venta->setFeria($data['name'] ?? $request->get('name'));

                    foreach ($stock->getProductos() as $productoStock) {
                        $productoVenta = new ProductoVenta();
                        $productoVenta->setVenta($venta);
                        $productoVenta->setProducto($productoStock->getProducto());
                        $entityManager->persist($productoVenta);
                        foreach ($productoStock->getTallas() as $tallaStock) {
                            $tallaVenta = new TallaVenta($tallaStock->getTalla());
                            $tallaVenta->setTallaStock($tallaStock);
                            $tallaVenta->setCantidad($tallaStock->getCantidad());
                            $tallaVenta->setProducto($productoVenta);
                            $entityManager->persist($tallaVenta);
                        }
                    }
                    $entityManager->persist($venta);
                    $entityManager->flush();
                    $data['id'] = $venta->getId();
                } else {
                    if (isset($data['close'])) {
                        if (!($venta = $entityManager->getRepository('App:Venta')->findOneBy(
                            ['open' => true, 'user' => $this->getUser()]
                        ))) {
                            return new JsonResponse($data);
                        }
                        $venta->setOpen(false);
                        $venta->setCloseAt(new \DateTime());
                        foreach ($stock->getProductos() as $producto) {
                            foreach ($producto->getTallas() as $talla) {
                                $talla->setVendidas(0);
                                $entityManager->persist($talla);
                            }
                        }
                        $entityManager->persist($venta);
                        $entityManager->flush();
                    }
                }
            }

            return new JsonResponse($data);
        }
        else
            return new JsonResponse(['error' => 'no stock for this user']);
    }

    /**
     * @Route(
     *     name="update_talla",
     *     path="/update-talla",
     *     methods={"POST"}
     * )
     */
    public function updateTalla(EntityManagerInterface $entityManager)
    {
        if ($tallas = Util::decodeBody()) {
            foreach ($tallas as $value) {
                if (!($talla = $entityManager->getRepository('App:TallaStock')->find($value['id']))) {
                    continue;
                }
                $talla->setVendidas($value['cantidad']);
                $talla->setLastUpdate(new \DateTime());
                $tallaVenta = $entityManager->getRepository('App:Venta')->findTallaByTallaStock($talla);
                $tallaVenta->setVendidas($value['cantidad']);
                $tallaVenta->setLastUpdate(new \DateTime());
                $entityManager->persist($talla, $tallaVenta);
                if (!isset($venta)) {
                    $venta = $tallaVenta->getProducto()->getVentaEntity();
                }
            }
            if (isset($venta)) {
                $venta->setLastUpdate(new \DateTime());
                $entityManager->persist($venta);
            };
            $entityManager->flush();

            return new JsonResponse(['updated']);
        }

        return new JsonResponse(['no data']);
    }

    /**
     * @Route(
     *     name="return_product",
     *     path="/return-product",
     *     methods={"POST"}
     * )
     */
    public function returnProduct(EntityManagerInterface $entityManager)
    {
        if ($tallas = Util::decodeBody()) {
            foreach ($tallas as $value) {
                if (!($talla = $entityManager->getRepository('App:TallaStock')->find($value['id']))) {
                    continue;
                }
                $talla->returnProduct($value['cantidad']);
                $talla->setLastUpdate(new \DateTime());
                $tallaVenta = $entityManager->getRepository('App:Venta')->findTallaByTallaStock($talla);
                $tallaVenta->returnProduct($value['cantidad']);
                $tallaVenta->setLastUpdate(new \DateTime());
                $entityManager->persist($talla, $tallaVenta);
                if (!isset($venta)) {
                    $venta = $tallaVenta->getProducto()->getVentaEntity();
                }
            }
            if (isset($venta)) {
                $venta->setLastUpdate(new \DateTime());
                $entityManager->persist($venta);
            };
            $entityManager->flush();

            return new JsonResponse(['updated']);
        }

        return new JsonResponse(['no data']);
    }


}

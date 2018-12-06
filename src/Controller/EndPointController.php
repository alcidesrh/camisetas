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
     *     name="check_feria",
     *     path="/feria",
     *     methods={"POST"}
     * )
     */
    public function checkFeria(EntityManagerInterface $entityManager)
    {
        if ($data = Util::decodeBody()) {

            if (!$entityManager->getRepository('App:Venta')->findBy(
                    ['open' => true, 'user' => $this->getUser()]
                ) && isset($data['name'])) {
                $venta = new Venta();
                $venta->setUser($this->getUser());
                $venta->setFeria($data['name']);

                $stock = $this->getUser()->getStock();
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
            } else {
                if (isset($data['close'])) {
                    if (!($venta = $entityManager->getRepository('App:Venta')->findOneBy(
                        ['open' => true, 'user' => $this->getUser()]
                    ))) {
                        return new JsonResponse($data);
                    }
                    $venta->setOpen(false);
                    $venta->setCloseAt(new \DateTime());
                    $stock = $this->getUser()->getStock();
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
                $talla->setVendidas($value['vendida']);
                $talla->setLastUpdate(new \DateTime());
                $tallaVenta = $entityManager->getRepository('App:Venta')->findTallaByTallaStock($talla);
                $tallaVenta->setVendidas($value['vendida']);
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

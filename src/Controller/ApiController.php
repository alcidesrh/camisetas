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

use App\Entity\File;
use App\Entity\ProductoVenta;
use App\Entity\Stock;
use App\Entity\Producto;
use App\Entity\ProductoStock;
use App\Entity\TallaStock;
use App\Entity\TallaVenta;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Util;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/api")
 * @author Alcides Rodríguez <alcdesrh@gmail.com>
 */
class ApiController extends AbstractController
{

    /**
     * @Route("/save-producto", name="save_producto")
     */
    public function saveProduct(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        $data = json_decode(stripslashes($request->get('producto')), true);
        $producto = isset($data['id']) ? $entityManager->getRepository('App:Producto')->find(
            $data['id']
        ) : new Producto();
        if (isset($data['imgName'])) {
            $pathinfo = pathinfo($data['imgName']);
            $base64img = $data['imgSrc'];
            if ($pathinfo['extension'] == 'png' || $pathinfo['extension'] == 'gif') {
                $img = str_replace('data:image/png;base64,', '', $base64img);
            } else {
                $img = str_replace('data:image/jpeg;base64,', '', $base64img);
            }
            $base64_decode = base64_decode(str_replace(' ', '+', $img));
            $fileName = date_timestamp_get(new \DateTime());
            $fs = new Filesystem();
            try {
                $fs->dumpFile(File::dir.$fileName, $base64_decode);
                $imagen = new File();
                $imagen->setName(strtolower(preg_replace('/[^A-Za-z0-9]+/', "", $pathinfo['filename'])))
                    ->setExt($pathinfo['extension'])->setPath($fileName);
                !$producto->getImagen() ?? $entityManager->remove($producto->getImagen());
                $producto->setImagen($imagen);
            } catch (\Exception $exception) {

            }
        }
        $producto->setNombre($data['nombre']);
//        $tallas = [];
//        for ($i = 0; $i < count($data['tallas']); $i++) {
//            if (!$data['tallas'][$i]) {
//                continue;
//            }
//            if (is_array($data['tallas'][$i])) {
//                $array = $data['tallas'][$i];
//                $talla = $entityManager->find('App:TallaStock', $array['talla']);
//                $tallas[] = $talla;
//                $talla->setCantidad($data['stock'][$i]);
//                $entityManager->persist($talla);
//            } else {
//                $talla = new TallaStock();
//                $talla->setTalla($entityManager->getRepository('App:Talla')->find($data['tallas'][$i]))->setCantidad(
//                    $data['stock'][$i]
//                );
//                $producto->addTallas($talla);
//            }
//        }
//        if (isset($data['id'])) {
//            foreach ($producto->getTallas() as $value) {
//                if ($value->getId() && !in_array($value->getId(), $tallas)) {
//                    $producto->removeTallas($value);
//                }
//            }
//        }
        $entityManager->persist($producto);
        $entityManager->flush();

        return new JsonResponse(['save']);
    }

    /**
     * @Route(
     *     name="insert_user",
     *     path="/users",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=User::class,
     *         "_api_collection_operation_name"="insert_user"
     *     }
     * )
     * @Route(
     *     name="edit_user",
     *     path="/users/{id}",
     *     methods={"PUT"},
     *     defaults={
     *         "_api_resource_class"=User::class,
     *         "_api_item_operation_name"="edit_user"
     *     }
     * )
     */
    public function insertUser(User $data, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        if (!$data->getId()) {
            $data->setPassword($encoder->encodePassword($data, $data->getPassword()));
        }
        $entityManager->persist($data);
        $entityManager->flush();

        return $data;
    }

    /**
     * @Route(
     *     name="user_no_stock",
     *     path="/users/no-stock",
     *     methods={"GET"},
     *     defaults={
     *         "_api_resource_class"=User::class,
     *         "_api_collection_operation_name"="no-stock"
     *     }
     * )
     */
    public function getUserNoStock(UserRepository $repository)
    {
        return $repository->findUserNoStock();
    }

    /**
     * @Route("/change-password", name="change_password")
     */
    public function changepassword(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ): JsonResponse {
        $data = Util::decodeBody();
        $user = $entityManager->getRepository('App:User')->find($data['id']);
        $user->setPassword($encoder->encodePassword($user, $data['password']));
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['save']);
    }

    /**
     * @Route("/save-stock", name="save_stock")
     *
     */
    public function saveStock(EntityManagerInterface $entityManager): JsonResponse
    {
        $data = Util::decodeBody();
        $stock = new Stock();
        $stock->setUser($entityManager->getRepository('App:User')->find($data['user']));
        $productos = $entityManager->getRepository('App:Producto')->getProductosByIds($data['productos']);
        $cont = -1;
        foreach ($productos as $producto) {
            $cont++;
            $productoStock = new ProductoStock();
            $productoStock->setProducto($producto);
            $productoStock->setStock($stock);
            $entityManager->persist($productoStock);
            for ($i = 0; $i < count($data['stock']); $i++) {
                $value = $data['stock'][$i];
                $talla = new TallaStock($entityManager->getRepository('App:Talla')->find($value['id']));
                $talla->setProducto($productoStock);
                $entityManager->persist($talla);
                if (!is_null($data['productos'][$cont]['stock'][$i]['stock'])) {
                    $talla->setCantidad($data['productos'][$cont]['stock'][$i]['stock'] ?? 0);
                } else {
                    $talla->setCantidad($value['stock'] ?? 0);
                }
                $productoStock->addTallas($talla);
            }
        }
        $entityManager->persist($stock);
        $entityManager->flush();

        return new JsonResponse(['save']);
    }

    /**
     * @Route("/edit-stock/{id}", name="edit_stock")
     *
     */
    public function editStock(Stock $stock, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = Util::decodeBody();

        $array = new ArrayCollection();
        $arrayVenta = new ArrayCollection();
        $venta = $entityManager->getRepository('App:Venta')->findOneBy(['open' => true]);
        foreach ($data['productos'] as $item) {
            if (isset($item['producto_stock'])) {
                $productoStock = $entityManager->getRepository('App:ProductoStock')->find(
                    $item['producto_stock']
                );
                if($productoVenta = $entityManager->getRepository('App:Venta')->findProductoVentaByProductoStock($productoStock)){
                    $productoVenta = $entityManager->getRepository('App:ProductoVenta')->find(intval(array_values($productoVenta)[0]));
                    $arrayVenta->add($productoVenta);
                }
                $array->add($productoStock);
                for ($i = 0; $i < count($data['stock']); $i++) {
                    $value = $data['stock'][$i];
                    if (!($talla = $productoStock->getTalla($i))) {
                        $talla = new TallaStock($entityManager->getRepository('App:Talla')->find($value['id']));
                        $talla->setProducto($productoStock);
                        $productoStock->addTallas($talla);
                        if($venta){
                            $tallaVenta = new TallaVenta($talla->getTalla());
                            $tallaVenta->setProducto($productoVenta);
                            $productoVenta->addTallas($tallaVenta);
                        }
                    }
                    else if($venta){
                        $tallaVenta = $entityManager->getRepository('App:Venta')->findTallaByTallaStock($talla);
                    }
                    if ($item['stock'][$i]['stock']) {
                        $talla->setCantidad($item['stock'][$i]['stock']);
                        if($venta)
                        $tallaVenta->setCantidad($item['stock'][$i]['stock']);
                    } else {
                        $talla->setCantidad($value['stock'] ?? 0);
                        if($venta)
                        $tallaVenta->setCantidad($value['stock'] ?? 0);
                    }

                    $entityManager->persist($talla, $tallaVenta);
                }
            } else {
                $producto = $entityManager->getRepository('App:Producto')->find($item['id']);
                $productoStock = new ProductoStock();
                $productoStock->setProducto($producto);
                $productoStock->setStock($stock);
                $entityManager->persist($productoStock);
                if($venta){
                    $productoVenta = new ProductoVenta();
                    $productoVenta->setVenta($venta);
                    $productoVenta->setProducto($producto);
                    $entityManager->persist($productoVenta);
                }

                for ($i = 0; $i < count($data['stock']); $i++) {
                    $value = $data['stock'][$i];
                    $talla = new TallaStock($entityManager->getRepository('App:Talla')->find($value['id']));
                    $talla->setProducto($productoStock);
                    if ($item['stock'][$i]['stock']) {
                        $talla->setCantidad($item['stock'][$i]['stock']);
                    } else {
                        $talla->setCantidad($value['stock'] ?? 0);
                    }
                    $entityManager->persist($talla);
                    if($venta){
                        $tallaVenta = new TallaVenta($talla->getTalla());
                        $tallaVenta->setProducto($productoVenta);
                        $tallaVenta->setTallaStock($talla);
                        if ($item['stock'][$i]['stock']) {
                            $tallaVenta->setCantidad($item['stock'][$i]['stock']);
                        } else {
                            $tallaVenta->setCantidad($value['stock'] ?? 0);
                        }
                        $entityManager->persist($tallaVenta);
                    }
                }
            }

        }
        foreach ($stock->getProductos() as $value) {
            if (!$array->contains($value)) {
                $stock->removeProduct($value);
            }
        }
        $stock->setLastUpdate(new \DateTime());
        $stock->setRefresh(true);
        $entityManager->persist($stock);

        if($venta){
            foreach ($venta->getProductos() as $value) {
                if (!$arrayVenta->contains($value) && !$value->getVenta()) {
                    $venta->removeProduct($value);
                }
            }
            $stock->setLastUpdate(new \DateTime());
            $entityManager->persist($venta);
        }
        $entityManager->flush();

        return new JsonResponse(['save']);
    }

    /**
     * @Route(
     *     name="stocks_user",
     *     path="/stocks-user/{id}",
     *     methods={"POST"},
     *     defaults={
     *         "_api_resource_class"=Stock::class,
     *         "_api_collection_operation_name"="stocks_user"
     *     }
     * )
     */
    public function userStocks(EntityManagerInterface $entityManager, $id)
    {
        return $entityManager->getRepository('App:Stock')->findBy(
            ['user' => $entityManager->find('App:User', $id)],
            ['createAt' => 'DESC']
        );
    }
}

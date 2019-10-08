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
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
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
        $producto->setSudadera($data['sudadera'] ?? null);
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
                if (!is_null($data['productos'][$cont]['stock'][$i]['stock']) && $data['productos'][$cont]['stock'][$i]['stock'] != 0) {
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
     * @Route("/clean-sale-stock", name="clean_sale_stock")
     *
     */
    public function cleanSaleStock(EntityManagerInterface $entityManager): JsonResponse
    {
        if ($stock = $this->getUser()->getStock()) {
            foreach ($stock->getProductos() as $producto) {
                foreach ($producto->getTallas() as $talla) {
                    $talla->setVendidas(0);
                    $entityManager->persist($talla);
                }
            }
            $entityManager->flush();
        }

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
        $venta = $entityManager->getRepository('App:Venta')->findOneBy(['open' => true, 'user' => $data['user']]);
        foreach ($data['productos'] as $item) {
            if (isset($item['producto_stock'])) {
                $productoStock = $entityManager->getRepository('App:ProductoStock')->find(
                    $item['producto_stock']
                );
                if ($venta)
                if ($productoVenta = $entityManager->getRepository('App:Venta')->findProductoVentaByProductoStock(
                    $productoStock
                )) {
                    $productoVenta = $entityManager->getRepository('App:ProductoVenta')->find(
                        intval(array_values($productoVenta)[0])
                    );
                    $arrayVenta->add($productoVenta);
                }
                $array->add($productoStock);
                for ($i = 0; $i < count($data['stock']); $i++) {
                    $value = $data['stock'][$i];
                    if (!($talla = $productoStock->getTalla($i))) {
                        $talla = new TallaStock($entityManager->getRepository('App:Talla')->find($value['id']));
                        $talla->setProducto($productoStock);
                        $productoStock->addTallas($talla);
                        if ($venta) {
                            $tallaVenta = new TallaVenta($talla->getTalla());
                            $tallaVenta->setProducto($productoVenta);
                            $productoVenta->addTallas($tallaVenta);
                        }
                    } else {
                        if ($venta) {
                            $tallaVenta = $entityManager->getRepository('App:Venta')->findTallaByTallaStock($talla);
                        }
                    }
                    if(!isset($data['add'])){
                        if ($item['stock'][$i]['stock']) {
                            $talla->setCantidad($item['stock'][$i]['stock']);
                            if ($venta) {
                                $tallaVenta->setCantidad($item['stock'][$i]['stock']);
                            }
                        }
//                        else {
//                            $talla->setCantidad($value['stock'] ?? 0);
//                            if ($venta) {
//                                $tallaVenta->setCantidad($value['stock'] ?? 0);
//                            }
//                        }
                    }
                    else{
                        if ($item['stock'][$i]['addCant']) {
                            $talla->addCantidad($item['stock'][$i]['addCant']);
                            if ($venta) {
                                $tallaVenta->addCantidad($item['stock'][$i]['addCant']);
                            }
                        }
//                        else {
//                            $talla->addCantidad($value['stock'] ?? 0);
//                            if ($venta) {
//                                $tallaVenta->addCantidad($value['stock'] ?? 0);
//                            }
//                        }
                    }


                    $entityManager->persist($talla);
                    if(isset($tallaVenta))$entityManager->persist($tallaVenta);
                }
            } else {
                $producto = $entityManager->getRepository('App:Producto')->find($item['id']);
                $productoStock = new ProductoStock();
                $productoStock->setProducto($producto);
                $productoStock->setStock($stock);
                $entityManager->persist($productoStock);
                if ($venta) {
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
                    if ($venta) {
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

        if ($venta) {
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

    /**
     * @Route(
     *     name="close_feria",
     *     path="/close-feria/{id}",
     *     methods={"POST"}
     * )
     */
    public function closeFeria(User $user, EntityManagerInterface $entityManager)
    {
        if($venta = $entityManager->getRepository('App:Venta')->findOneBy(['open' => true, 'user' => $user])){
            $venta->setOpen(false);
            $venta->setCloseAt(new \DateTime());
            $stock = $user->getStock();
            foreach ($stock->getProductos() as $producto) {
                foreach ($producto->getTallas() as $talla) {
                    $talla->setVendidas(0);
                    $entityManager->persist($talla);
                }
            }
            $entityManager->persist($venta);
            $entityManager->flush();
        }
        return new JsonResponse('close');
    }

    /**
     * @Route(
     *     name="replenish",
     *     path="/replenish",
     *     methods={"POST"}
     * )
     */
    public function replenish(EntityManagerInterface $entityManager)
    {
        if($data = Util::decodeBody()){
            
            $imprimir = $data['print'];

            if(!$imprimir){
                $tallas = $data['tallas'];
                foreach ($tallas as $value){
                    $talla = $entityManager->getRepository('App:TallaVenta')->find($value['talla']);

                    $tallaStock = $talla->getTallaStock();
                    $tallaStock->addCantidad($value['reponer']);
                    $entityManager->persist($tallaStock);
                }
                $entityManager->flush();
            }
            else{
                $productosPdf = [];
                $user = $entityManager->getRepository('App:Venta')->find($data['venta'])->getUser();
                $stock = $user->getStock();

                foreach ($stock->getProductos() as $producto){
                    $productoKey = $producto->getProducto()->getNombre();
                    foreach ($producto->getTallas() as $talla)
                    $productosStock[$productoKey][$talla->getTalla()->getNombre()]['cantidad'] = $talla->getCantidad();
                }
                $date = new \DateTime();
                $mpdf = new Mpdf(['tempDir' => 'pdf/temp/']);
                $mpdf->WriteHTML($this->renderView('pdf-resumen.html.twig', ['productos' => $productosPdf, 'user' => $user, 'productosStock' => $productosStock, 'fecha' => $date->format('d/m/Y h:i a')]));
                $mpdf->Output('pdf/reponer.pdf', Destination::FILE);
                return new JsonResponse('pdf/reponer.pdf');
            }
            return new JsonResponse('ok');
        }
        return new JsonResponse('empty');
    }

    /**
     * @Route("/remove-producto/{id}", name="remove_producto")
     */
    public function removeProduct(Producto $producto, EntityManagerInterface $entityManager): JsonResponse
    {

        foreach (array_merge(
            $entityManager->getRepository('App:ProductoStock')->findBy(['producto' => $producto]),
            $entityManager->getRepository('App:ProductoVenta')->findBy(['producto' => $producto])) as $item){
            $entityManager->remove($item);
        }
        $entityManager->flush();
        $entityManager->remove($producto);
        $entityManager->flush();

        return new JsonResponse(['remove']);
    }
}

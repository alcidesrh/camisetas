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
use App\Entity\Pedido;
use App\Entity\Producto;
use App\Entity\ProductoPedido;
use App\Entity\TallaStock;
use App\Entity\User;
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
     * * @Route(
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
     * @Route("/save-pedido", name="save_pedido")
     *
     */
    public function savePedido(EntityManagerInterface $entityManager): JsonResponse
    {
        $data = Util::decodeBody();
        $pedido = new Pedido();
        $pedido->setUser($entityManager->getRepository('App:User')->find($data['user']));
        $productos = $entityManager->getRepository('App:Producto')->getProductosByIds($data['productos']);
        $cont = -1;
        foreach ($productos as $producto) {
            $cont++;
            $productoPedido = new ProductoPedido();
            $productoPedido->setProducto($producto);
            $productoPedido->setPedido($pedido);
            $entityManager->persist($productoPedido);
            for ($i = 0; $i < count($data['stock']); $i++) {
                $value = $data['stock'][$i];
                $talla = new TallaStock($entityManager->getRepository('App:Talla')->find($value['id']));
                $talla->setProducto($productoPedido);
                $entityManager->persist($talla);
                if (!is_null($data['productos'][$cont]['stock'][$i]['stock'])) {
                    $talla->setCantidad($data['productos'][$cont]['stock'][$i]['stock'] ?? 0);
                } else {
                    $talla->setCantidad($value['stock'] ?? 0);
                }
                $productoPedido->addTallas($talla);
            }
        }
        $entityManager->persist($pedido);
        $entityManager->flush();

        return new JsonResponse(['save']);
    }

    /**
     * @Route("/edit-pedido/{id}", name="edit_pedido")
     *
     */
    public function editPedido(Pedido $pedido, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = Util::decodeBody();

        $array = new ArrayCollection();
        foreach ($data['productos'] as $item) {
            if (isset($item['producto_pedido'])) {
                $productoPedido = $entityManager->getRepository('App:ProductoPedido')->find(
                    $item['producto_pedido']
                );
                $array->add($productoPedido);
                for ($i = 0; $i < count($data['stock']); $i++) {
                    $value = $data['stock'][$i];
                    $talla = $productoPedido->getTalla($i);
                    if ($item['stock'][$i]['stock']) {
                        $talla->setCantidad( $item['stock'][$i]['stock'] );
                    } else {
                        $talla->setCantidad($value['stock'] ?? 0);
                    }
                    $entityManager->persist($talla);
                }
            } else {
                $producto = $entityManager->getRepository('App:Producto')->find($item['id']);
                $productoPedido = new ProductoPedido();
                $productoPedido->setProducto($producto);
                $productoPedido->setPedido($pedido);
                $entityManager->persist($productoPedido);
                for ($i = 0; $i < count($data['stock']); $i++) {
                    $value = $data['stock'][$i];
                    $talla = new TallaStock($entityManager->getRepository('App:Talla')->find($value['id']));
                    $talla->setProducto($productoPedido);
                    $entityManager->persist($talla);
                    if ( $item['stock'][$i]['stock'] ) {
                        $talla->setCantidad($item['stock'][$i]['stock']);
                    } else {
                        $talla->setCantidad($value['stock'] ?? 0);
                    }
                    $productoPedido->addTallas($talla);
                }
            }

        }
        foreach ($pedido->getProductos() as $value){
            if(!$array->contains($value))
                $pedido->removeProduct($value);
        }
        $pedido->setLastUpdate(new \DateTime());
        $pedido->setEdited(true);
        $entityManager->persist($pedido);
        $entityManager->flush();
        return new JsonResponse(['save']);
    }

    /**
     * @Route("/pedidos-user/{id}", name="pedidos_user")
     *
     */
    public function userPedidos(User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $result = [];
        foreach ($entityManager->getRepository('App:Pedido')->findBy(['user' => $user], ['createAt' => 'DESC']) as $value)
            $result[] = $value->userPedido();
        return new JsonResponse($result);
    }
}

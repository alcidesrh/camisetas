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
use App\Entity\Producto;
use App\Entity\TallaStock;
use App\Utils\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 * @author Alcides Rodríguez <alcdesrh@gmail.com>
 */
class ApiController extends AbstractController
{
    public function reloadAction()
    {
        $route = $this->get('router')->getContext()->getPathInfo();
        $route = str_getcsv($route, '/');
        unset($route[0]);
        $route = Util::array_string_values_nospace(array_merge($route));

        return $this->render('homepage.html.twig', ['route_reload' => $route]);
    }

    /**
     * @Route("/guardar-producto", name="api_guardar_producto")
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
        $tallas = [];
        for ($i = 0; $i < count($data['tallas']); $i++) {
            if (!$data['tallas'][$i]) {
                continue;
            }
            if (is_array($data['tallas'][$i])) {
                $array = $data['tallas'][$i];
                $talla = $entityManager->find('App:TallaStock', $array['talla']);
                $tallas[] = $talla;
                $talla->setCantidad($data['stock'][$i]);
                $entityManager->persist($talla);
            } else {
                $talla = new TallaStock();
                $talla->setTalla($entityManager->getRepository('App:Talla')->find($data['tallas'][$i]))->setCantidad(
                    $data['stock'][$i]
                );
                $producto->addTallas($talla);
            }
        }
        if (isset($data['id'])) {
            foreach ($producto->getTallas() as $value) {
                if ($value->getId() && !in_array($value->getId(), $tallas)) {
                    $producto->removeTallas($value);
                }
            }
        }
        $entityManager->persist($producto);
        $entityManager->flush();

        return new JsonResponse(['save']);
    }
}

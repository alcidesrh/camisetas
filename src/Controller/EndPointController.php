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

use App\Utils\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/endpoint")
 * @author Alcides Rodríguez <alcdesrh@gmail.com>
 */
class EndPointController extends AbstractController
{

    /**
     * @Route(
     *     name="check_feria",
     *     path="/feria",
     *     methods={"GET"}
     * )
     */
    public function checkFeria(EntityManagerInterface $entityManager)
    {
        if($pedidos = $entityManager->getRepository('App:Pedido')->checkPedidos($this->getUser())){
            foreach ($pedidos as $pedido){
                $pedido->setActive(true);
                $pedido->setEdited(false);
                $entityManager->persist($pedido);
            }
            $entityManager->flush();
        }
        return new JsonResponse([$pedidos]);
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
        if($tallas = Util::decodeBody()){
            foreach ($tallas as $value){
                $talla = $entityManager->getRepository('App:TallaStock')->find($value['id']);
                $talla->setVendidas($value['vendida']);
                $talla->setLastUpdate(new \DateTime());
                $entityManager->persist($talla);
            }
            $entityManager->flush();
            return new JsonResponse(['updated']);
        }
        return new JsonResponse(['no data']);
    }
}

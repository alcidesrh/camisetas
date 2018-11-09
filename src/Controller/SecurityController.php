<?php

namespace App\Controller;

use App\Utils\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class SecurityController extends AbstractController
{

    public function reloadAction()
    {
        $route = $this->get('router')->getContext()->getPathInfo();
        $route = str_getcsv($route, '/');
        unset($route[0]);
        $route = Util::array_string_values_nospace(array_merge($route));

        return $this->render('homepage.html.twig', ['route_reload' => $route]);
    }
}

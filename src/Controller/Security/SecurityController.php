<?php

namespace App\Controller\Security;

use App\Utils\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        if($user = $this->getUser() && !$this->isGranted('ROLE_ADMIN')){
            return $this->redirect($this->generateUrl('security_logout'));
        }
        $error = $authenticationUtils->getLastAuthenticationError() || $request->get('access_error', false);//$authenticationUtils->getLastAuthenticationError() ?? $request->get('access_error', "");
        return $this->render('security/login.html.twig', [
            // last username entered by the user (if any)
            'last_username' => $authenticationUtils->getLastUsername(),
            // last authentication error (if any)
            'error' => $error
        ]);
    }

    /**
     * @Route("/endpoint/login", name="api_login")
     */
    public function loginApi(Request $request)
    {
        $user = $this->getUser();

        return $this->json(array(
            'username' => 'admin',//$user->getUsername(),
            'password' => 'admin'//$user->getRoles(),
        ));
    }

    /**
     * This is the route the user can use to logout.
     *
     * But, this will never be executed. Symfony will intercept this first
     * and handle the logout automatically. See logout in config/packages/security.yaml
     *
         * @Route("/logout", name="security_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }


    public function reloadAction(EntityManagerInterface $entityManager)
    {
//        $cols = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];
//        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
//        $spreadsheet = $reader->load("STOCK KINI 07-11-2019.xlsx");
////        $cellValue = $spreadsheet->getActiveSheet()->getCell('A3')->getValue();
//
//        $stock = new Stock();
//        $stock->setUser($entityManager->getRepository('App:User')->find(2));
//
//        for ($i = 3; $i < 104; $i++){
//            if($i == 88 || $i == 89)continue;
//            $producto = $entityManager->getRepository('App:Producto')->findOneBy(['nombre' => $spreadsheet->getActiveSheet()->getCell("A$i")->getValue()]);
//            for ($j = 0; $j < count($cols); $j++){
//                $productoStock = new ProductoStock();
//                $productoStock->setProducto($producto);
//                $productoStock->setStock($stock);
//                $entityManager->persist($productoStock);
//
//                $talla = new TallaStock($entityManager->getRepository('App:Talla')->findOneBy(['nombre' => "{$cols[$j]}1"]));
//                $talla->setProducto($productoStock);
//                $talla->setCantidad($spreadsheet->getActiveSheet()->getCell("{$cols[$j]}$i")->getValue() ?? 0);
//
//                $entityManager->persist($talla);
//                $productoStock->addTallas($talla);
//            }
//        }
//        $entityManager->persist($stock);
//        $entityManager->flush();

        $route = $this->get('router')->getContext()->getPathInfo();
        $route = str_getcsv($route, '/');
        unset($route[0]);
        $route = Util::array_string_values_nospace(array_merge($route));

        return $this->render('homepage.html.twig', ['route_reload' => $route]);
    }
}

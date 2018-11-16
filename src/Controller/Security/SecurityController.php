<?php

namespace App\Controller\Security;

use App\Utils\Util;
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
        return $this->render('security/login.html.twig', [
            // last username entered by the user (if any)
            'last_username' => $authenticationUtils->getLastUsername(),
            // last authentication error (if any)
            'error' => $authenticationUtils->getLastAuthenticationError() ?? $request->get('access_error', ""),
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


    public function reloadAction()
    {
        $route = $this->get('router')->getContext()->getPathInfo();
        $route = str_getcsv($route, '/');
        unset($route[0]);
        $route = Util::array_string_values_nospace(array_merge($route));

        return $this->render('homepage.html.twig', ['route_reload' => $route]);
    }
}

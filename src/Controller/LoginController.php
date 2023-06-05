<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Security\LoginFormAuthenticator;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Security\Core\Security;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function indexLogin(Security $security, AuthenticationUtils $authenticationUtils): Response
    {

        // Obtener el usuario autenticado
        $user = $security->getUser();
        
        // Si el usuario está autenticado, obtén su ID
        $userId = $user ? $user->getId() : null;

         // get the login error if there is one
         $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
         $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'userID'        => $userId,
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout()
    {
        return $this->redirectToRoute('login');
    }

   
}

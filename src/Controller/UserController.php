<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    #[Route('/registration', name: 'userRegistration')]
    public function userRegistration(SessionInterface $session, UserPasswordHasherInterface $passwordHasher, Request $request): Response
    {
        $user = new User();
        $registration_form = $this->createForm(UserType::class, $user);
        $registration_form->handleRequest($request);

        if ($registration_form->isSubmitted() && $registration_form->isValid()) {
            $roles = $user->getRoles();
            if (in_array('ROLE_ADMIN', $roles)) {
                $roles = ['ROLE_ADMIN'];
            } else {
                $roles = ['ROLE_USER'];
            }
            $user->setRoles($roles);

            $plaintextPassword = $registration_form->get('password')->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );

            $user->setPassword($hashedPassword);
            //enviar mensaje flash en caso de que el email de la cuenta ya exista.
            try {
                $this->em->persist($user);
                $this->em->flush();
                return $this->redirectToRoute('login');
            } catch (UniqueConstraintViolationException $e) {
                $session->getFlashBag()->add('error', 'Esta cuenta ya existe en la base de datos.');
            }

        }

        return $this->render('user/index.html.twig', [
            'registration_form' => $registration_form->createView(),
        ]);
    }
}

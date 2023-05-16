<?php

namespace App\Controller;

use App\Entity\UserAsignatura;
use App\Form\UserAsignaturaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\AsignaturaRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserAsignaturaController extends AbstractController
{
    #[Route('/principal/newuserasignatura', name: 'newuserasignatura', methods: ['GET', 'POST'])]
    public function new(UserRepository $userRepository, AsignaturaRepository $asignaturaRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $userAsignatura = new UserAsignatura();
        // Dentro del controlador
        $users = $userRepository->findAll(); // Obtener los usuarios desde el repositorio
        $asignaturas = $asignaturaRepository->findAll(); // Obtener las asignaturas desde el repositorio
        
        $usersOptions = [];
        foreach ($users as $user) {
            $usersOptions[$user->getId()] = $user; // Asignar el objeto User como valor
        }

        $asignaturasOptions = [];
        foreach ($asignaturas as $asignatura) {
            $asignaturasOptions[$asignatura->getId()] = $asignatura; // Asignar el objeto Asignatura como valor
        }


        $form = $this->createForm(UserAsignaturaType::class, $userAsignatura, [
            'users' => $usersOptions, // Array de opciones para los usuarios
            'asignaturas' => $asignaturasOptions, // Array de opciones para las asignaturas
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userAsignatura);
            $entityManager->flush();

            // Redireccionar o realizar alguna acción adicional después de guardar la entidad

            return $this->redirectToRoute('principal');
        }

        return $this->render('userasignatura/newuserasignatura.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

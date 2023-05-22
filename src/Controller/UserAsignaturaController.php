<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Asignatura;
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
        // Dentro del controlador
        $users = $userRepository->findAll(); // Obtener los usuarios desde el repositorio
        $asignaturas = $asignaturaRepository->findAll(); // Obtener las asignaturas desde el repositorio
        
        $form = $this->createForm(UserAsignaturaType::class, null, [
            'users' => $users, // Pasar la lista de usuarios al formulario
            'asignaturas' => $asignaturas, // Pasar la lista de asignaturas al formulario
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $data['user']; // Obtener el objeto User seleccionado en el formulario
            $asignaturas = $data['asignaturas']; // Obtener los objetos Asignatura seleccionados en el formulario

            foreach ($asignaturas as $asignatura) {
                $user->addAsignatura($asignatura); // Agregar la asignatura al usuario
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // Redireccionar o realizar alguna acción adicional después de guardar los cambios

            return $this->redirectToRoute('principal');
        }

        return $this->render('userasignatura/newuserasignatura.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

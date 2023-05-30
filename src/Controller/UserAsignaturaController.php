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
use App\Form\DesmatriculaType;


class UserAsignaturaController extends AbstractController
{
    #[Route('/principal/newuserasignatura', name: 'newuserasignatura', methods: ['GET', 'POST'])]
    public function new(UserRepository $userRepository, AsignaturaRepository $asignaturaRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $users = $userRepository->findAll(); // Obtener los usuarios desde el repositorio
        $asignaturas = $asignaturaRepository->findAll(); // Obtener las asignaturas
        
        $form = $this->createForm(UserAsignaturaType::class, null, [
            'users' => $users, // Pasar la lista de usuarios al formulario
            'asignaturas' => $asignaturas, // Pasar la lista de asignaturas 
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = $data['user']; // Obtener el User seleccionado en el formulario
            $asignaturas = $data['asignaturas']; // Obtener las Asignatura seleccionadas

            foreach ($asignaturas as $asignatura) {
                $user->addAsignatura($asignatura); // Agregar la asignatura al usuario
            }

            $entityManager->persist($user);
            $entityManager->flush();


            
            return $this->redirectToRoute('principal');
        }

        return $this->render('userasignatura/newuserasignatura.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/principal/desmatricula', name: 'desmatricula', methods: ['GET', 'POST'])]
    public function desmatricula(UserRepository $userRepository, AsignaturaRepository $asignaturaRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $users = $userRepository->findAll(); 
        $asignaturas = $asignaturaRepository->findAll(); 

        $form = $this->createForm(DesmatriculaType::class, null, [
            'users' => $users, 
            'asignaturas' => $asignaturas, 
        ]);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $user = $data['user']; 
            $asignaturas = $data['asignaturas']; 

            foreach ($asignaturas as $asignatura) {
                $user->removeAsignatura($asignatura); // Remover la asignatura del usuario
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('principal');
        }

        return $this->render('userasignatura/desmatricula.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


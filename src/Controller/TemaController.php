<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Tema;
use App\Form\TemaType;
use App\Repository\TemaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TemaController extends AbstractController
{
    #[Route('/principal/newtema/{id}', name: 'newtema', methods: ['GET', 'POST'])]

    public function nuevoTema(Security $security, Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $tema = new Tema();

        $asignatura = $entityManager->getRepository(Asignatura::class)->find($id); // Obtener la asignatura según su ID

        $tema->setAsignatura($asignatura); // Asignar la asignatura al tema

        $form = $this->createForm(TemaType::class, $tema);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $usuario = $security->getUser();
            $tema->setUser($usuario);
            $asignatura = $entityManager->getRepository(Asignatura::class)->find($id);
            $tema->setAsignatura($asignatura);
            $entityManager->persist($tema);
            $entityManager->flush();

            return $this->redirectToRoute('principal');
        }

        return $this->render('tema/newtema.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

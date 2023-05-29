<?php

namespace App\Controller;

use App\Entity\Entrega;
use App\Entity\Subida;
use App\Entity\Nota;
use App\Form\NotaType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotaController extends AbstractController
{
    #[Route('/principal/newnota/{id}', name: 'newnota', methods: ["GET"])]
    public function asignarNota(ManagerRegistry $doctrine, Request $request, Subida $subida, Entrega $entrega, $id)
    {
        // Verificar si la subida existe
        if (!$subida) {
            throw $this->createNotFoundException('La subida no existe.');
        }

        // Crear una nueva instancia de Nota
        $nota = new Nota();

        // Crear el formulario
        $form = $this->createForm(NotaType::class, $nota);

        // Manejar la solicitud del formulario
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Asignar la subida a la nota
            $nota->setSubida($subida);

            // Guardar la nota en la base de datos
            $entityManager = $doctrine->getManager();
            $entityManager->persist($nota);
            $entityManager->flush();

            // Redirigir a la página de la subida o a donde desees
            return $this->redirectToRoute('entrega', ['id' => $entrega->getId()]);
        }

        return $this->render('entrega/newnota.html.twig', [
            'form' => $form->createView(),
            'subida' => $subida,
        ]);
    }
}

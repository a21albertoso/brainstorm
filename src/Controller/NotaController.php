<?php

namespace App\Controller;

use App\Entity\Entrega;
use App\Entity\Subida;
use App\Entity\Nota;
use App\Form\NotaType;
use App\Repository\SubidaRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotaController extends AbstractController
{
    #[Route('/principal/newnota/{id}', name: 'newnota', methods: ['GET', 'POST'])]
    public function asignarNota(SubidaRepository $subidaRepository, ManagerRegistry $doctrine, Request $request, $id)
{
    $entityManager = $doctrine->getManager();
    
    // Obtener la entidad Subida por ID
    $subida = $subidaRepository->find($id);

    // Verificamos si la subida existe
    if (!$subida) {
        throw $this->createNotFoundException('La subida no existe.');
    }

    // Obtener la nota existente
    $notaExistente = $subida->getNota();

    // Crear nueva nota o usar la que hay
    if ($notaExistente) {
        $nota = $notaExistente;
    } else {
        $nota = new Nota();
    }

    // Crear el formulario
    $form = $this->createForm(NotaType::class, $nota);

    // Manejar la solicitud del formulario
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Asignar la subida a la nota
        $nota->setSubida($subida);

        // Guardar la nueva nota o actualizar la existente
        if ($notaExistente) {
            $notaExistente->setNumero($nota->getNumero());
        } else {
            $entityManager->persist($nota);
        }

        $entityManager->flush();

        // Redirigir a la página de la subida o a donde desees
        return $this->redirectToRoute('entrega', ['id' => $subida->getEntrega()->getId()]);
    }

    return $this->render('entrega/newnota.html.twig', [
        'form' => $form->createView(),
        'subida' => $subida,
    ]);
}

}

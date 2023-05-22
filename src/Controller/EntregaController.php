<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Entrega;
use App\Form\EntregaType;
use App\Repository\EntregaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class EntregaController extends AbstractController
{
    #[Route('/principal/newentrega/{id}', name: 'newentrega', methods: ['GET', 'POST'])]
    public function nuevaEntrega(Security $security, Request $request, EntityManagerInterface $entityManager, $id): Response
    {
        $entrega = new Entrega();

        $asignatura = $entityManager->getRepository(Asignatura::class)->find($id); // Obtener la asignatura según su ID

        $entrega->setAsignatura($asignatura); // Asignar la asignatura a la entrega

        $form = $this->createForm(EntregaType::class, $entrega);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $usuario = $security->getUser();
            $entrega->setUser($usuario);
            $asignatura = $entityManager->getRepository(Asignatura::class)->find($id);
            $entrega->setAsignatura($asignatura);
            $entityManager->persist($entrega);
            $entityManager->flush();

            return $this->redirectToRoute('principal');
        }

        return $this->render('entrega/newentrega.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/principal/entrega/{id}', name: 'entrega')]
public function entrega(EntregaRepository $entregaRepository, $id): Response
{
    $entrega = $entregaRepository->find($id);

    return $this->render('entrega/entrega.html.twig', [
        'entrega' => $entrega,
    ]);
}

}

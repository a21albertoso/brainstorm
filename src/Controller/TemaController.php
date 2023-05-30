<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Tema;
use App\Form\TemaType;
use App\Repository\TemaRepository;
use App\Service\ColorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TemaController extends AbstractController
{

    private $colorService;

    public function __construct(ColorService $colorService)
    {
        $this->colorService = $colorService;
    }

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





    #[Route('principal/tema/{id}', name: 'tema')]
    public function Tema(TemaRepository $temaRepository, $id): Response
    {

        $tema = $temaRepository->find($id);

        $randomColor = $this->colorService->getRandomColor();


        return $this->render('tema/tema.html.twig', [
            'tema' => $tema,
            'randomColor' => $randomColor,
        ]);
    }



}

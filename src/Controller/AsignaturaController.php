<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Entrega;
use App\Entity\Tema;
use App\Form\AsignaturaType;
use App\Repository\EntregaRepository;
use App\Repository\TemaRepository;
use App\Service\ColorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AsignaturaController extends AbstractController
{
    private $em;
    private $colorService;

    public function __construct(EntityManagerInterface $em, ColorService $colorService)
    {
        $this->em = $em;
        $this->colorService = $colorService;
    }

    #[Route('principal/asignatura/{id}', name: 'asignatura')]
    public function show(EntregaRepository $entregaRepository, TemaRepository $temaRepository, Request $request, AuthenticationUtils $authenticationUtils, $id)
    {
        // Obtener la asignatura por ID
        $asignatura = $this->em->getRepository(Asignatura::class)->find($id);

        if (!$asignatura) {
            throw $this->createNotFoundException('Asignatura no encontrada');
        }

        // Obtener los temas de la asignatura
        $temas = $temaRepository->findBy(['asignatura' => $asignatura]);
        $entregas = $entregaRepository->findBy(['asignatura' => $asignatura]);

        //correo del usuario
        $lastUsername = $authenticationUtils->getLastUsername();

        // enlaces para eliminar temas
        if ($request->query->has('borrartema')) { //el error era porque tenía boraar
            $temaId = $request->query->get('borrartema');
            $tema = $temaRepository->find($temaId);

            if ($tema) {
                $this->em->remove($tema);
                $this->em->flush();
            }

            return $this->redirectToRoute('asignatura', ['id' => $id]);
        }

        $randomColor = $this->colorService->getRandomColor();


        return $this->render('asignatura/asignatura.html.twig', [
            'asignatura' => $asignatura,
            'temas' => $temas,
            'entregas' => $entregas,
            'last_username' => $lastUsername,
            'randomColor' => $randomColor,
        ]);
    }

    #[Route('/asignatura/nueva', name: 'newasignatura', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine, Request $request)
    {
        $asignatura = new Asignatura();

        $form = $this->createForm(AsignaturaType::class, $asignatura);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($asignatura);
            $entityManager->flush();

            // Redirigir a la página principal u otra página relevante
            return $this->redirectToRoute('homepage');
        }

        return $this->render('asignatura/newasignatura.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
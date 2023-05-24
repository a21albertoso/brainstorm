<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Entrega;
use App\Entity\Tema;
use App\Service\ColorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function show(AuthenticationUtils $authenticationUtils, $id)
    {
        // Obtener la asignatura por ID
        $asignatura = $this->em->getRepository(Asignatura::class)->find($id);

        if (!$asignatura) {
            throw $this->createNotFoundException('Asignatura no encontrada');
        }

        // Obtener los temas de la asignatura
        $temas = $this->em->getRepository(Tema::class)->findBy(['asignatura' => $asignatura]);
        $entregas = $this->em->getRepository(Entrega::class)->findBy(['asignatura' => $asignatura]);

        //correo del usuario
        $lastUsername = $authenticationUtils->getLastUsername();

        $randomColor = $this->colorService->getRandomColor();


        return $this->render('asignatura/asignatura.html.twig', [
            'asignatura' => $asignatura,
            'temas' => $temas,
            'entregas' => $entregas,
            'last_username' => $lastUsername,
            'randomColor' => $randomColor,
        ]);
    }
}
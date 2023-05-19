<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Tema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AsignaturaController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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

        //correo del usuario
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('asignatura/asignatura.html.twig', [
            'asignatura' => $asignatura,
            'temas' => $temas,
            'last_username' => $lastUsername,
        ]);
    }
}
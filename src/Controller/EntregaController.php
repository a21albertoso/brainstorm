<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Entrega;
use App\Entity\Nota;
use App\Entity\Subida;
use App\Form\EntregaType;
use App\Form\SubidaType;
use App\Repository\EntregaRepository;
use App\Repository\SubidaRepository;
use App\Service\ColorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Form\NotaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


class EntregaController extends AbstractController
{

    private $colorService;

    public function __construct(ColorService $colorService)
    {
        $this->colorService = $colorService;
    }

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
    public function entrega(ManagerRegistry $doctrine, SluggerInterface $slugger, SessionInterface $session, Security $security, Request $request, EntregaRepository $entregaRepository, SubidaRepository $subidaRepository, EntityManagerInterface $entityManager, $id): Response
    {
        $entrega = $entregaRepository->find($id);
        $subidas = $subidaRepository->findAll();
        $subida = new Subida();


        $user = $this->getUser();

    // Obtener la última subida del usuario
    $latestSubida = $subidaRepository->findLatestSubidaByUserAndEntrega($user, $entrega);

    $latestNota = $subidaRepository->findLatestSubidaByUserAndEntrega($user, $entrega);

            $nota = null;
        if ($latestNota && $latestNota->getUser() === $user) {
            $nota = $latestNota->getNota();
        }


    
        $formArchivo = $this->createForm(SubidaType::class, $subida);
        $formArchivo->handleRequest($request);

        if ($formArchivo->isSubmitted() && $formArchivo->isValid()) {
            $archivo = $formArchivo->get('file')->getData();
    
            $user = $security->getUser();

            if ($archivo) {
                $originalFilename = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.$user->getUserIdentifier().uniqid().'.'.$archivo->guessExtension();
    
                try {
                    $archivo->move(
                        $this->getParameter('archivos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception("Hay un problema con tu foto de perfil");
                }
    
                $subida->setFile($newFilename);
                $subida->setFechaSubida(new \DateTime());

                $subida->setEntrega($entrega); // Establecer la relación con la entrega

                $user = $security->getUser(); // Establecer la relación con el user
                if ($user instanceof UserInterface) {
                    $subida->setUser($user);
                }

            }
    
            $entityManager = $doctrine->getManager();
            $entityManager->persist($subida);
            $entityManager->flush();
    
            return $this->redirectToRoute('entrega', ['id' => $id]);

        }

        


    // para las descargas.
    $subidasEntrega = $subidaRepository->findBy(['entrega' => $entrega]);

    $notaMedia = 0;

    $randomColor = $this->colorService->getRandomColor();

    return $this->render('entrega/entrega.html.twig', [
        'entrega' => $entrega,
        'randomColor' => $randomColor,
        'subida' => $subida,
        'latestSubida' => $latestSubida,
        'formArchivo' => $formArchivo->createView(),
        'subidasEntrega' => $subidasEntrega, // Pasar las subidas a la plantilla
        'nota' => $nota,
        'latestNota' => $latestNota,
        'notaMedia' => $notaMedia, // Pasar la nota media al template
    ]);
}
    
}


<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Entrega;
use App\Entity\Subida;
use App\Form\EntregaType;
use App\Repository\EntregaRepository;
use Doctrine\ORM\EntityManagerInterface;
use InformacionAdicionalType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
    public function entrega(SessionInterface $session, Security $security, Request $request, EntregaRepository $entregaRepository, $id, EntityManagerInterface $entityManager): Response
    {
        $entrega = $entregaRepository->find($id);
        $subida = new Subida();
    
        $formularioSubida = $this->createFormBuilder($subida)
            ->add('file', FileType::class, [
                'label' => 'Archivo: ',
                'required' => true,
            ])
            ->getForm();
    
        $formularioSubida->handleRequest($request);
        if ($formularioSubida->isSubmitted() && $formularioSubida->isValid()) {
            $archivo = $formularioSubida->get('file')->getData();
    
            // Obtener el usuario actual
            $usuarioActual = $security->getUser();
    
            // Buscar una subida existente por el usuario y la tarea
            $subidaExistente = $entityManager->getRepository(Subida::class)->findOneBy([
                'entrega' => $entrega,
                'user' => $usuarioActual,
            ]);
    
            if ($subidaExistente) {
                // Reemplazar el archivo en la subida existente
                $subidaExistente->setFile($archivo);
                $subidaExistente->setFechaSubida(new \DateTime());
            } else {
                // Guardar información de la subida
                $subida->setFile($archivo);
                $subida->setFechaSubida(new \DateTime());
                $subida->setEntrega($entrega);
                
                // Asignar el usuario actual a la subida
                $subida->setUser($usuarioActual);
    
                $entityManager->persist($subida);
            }
    
            $entityManager->flush();
    
            // Establecer variable de sesión o propiedad de Subida para indicar que el archivo fue enviado
            $this->addFlash('success', 'Archivo enviado correctamente');

            $rutaTemporal = $archivo->getRealPath();
    
             $contenidoArchivo = file_get_contents($rutaTemporal);

        // Establecer la variable de sesión para mostrar la información del archivo en el Twig
        $session->set('contenido_archivo', $contenidoArchivo);

        return $this->redirectToRoute('entrega', ['id' => $id]);
        }
    

    $formularioNota = $this->createFormBuilder($subida)
        ->add('nota', NumberType::class, [
            'label' => 'Nota: ',
            'required' => false,
            'html5' => true,
            'scale' => 2, // Configurar el número de decimales permitidos
        ])
        ->getForm();

        $formularioNota = $this->createFormBuilder($subida)
        ->add('nota', NumberType::class, [
            'label' => 'Nota: ',
            'required' => false,
            'html5' => true,
            'scale' => 2, // Configurar el número de decimales permitidos
        ])
        ->getForm();
    
        $formularioNota = $this->createFormBuilder($subida)
        ->add('nota', NumberType::class, [
            'label' => 'Nota: ',
            'required' => false,
            'html5' => true,
            'scale' => 2, // Configurar el número de decimales permitidos
        ])
        ->getForm();
    
    $formularioNota->handleRequest($request);
    if ($formularioNota->isSubmitted() && $formularioNota->isValid()) {
        $nota = $formularioNota->get('nota')->getData();
    
        // Obtener la subida actual
        $subidaActual = $entrega->getSubidas()->first();
    
        if ($subidaActual) {
            // Guardar la nota en la subida actual
            $subidaActual->setNota($nota);
            $entityManager->flush();
        }
    
        $this->addFlash('success', 'Nota agregada correctamente');
    
        return $this->redirectToRoute('entrega', ['id' => $id]);
    }
    
    // Obtener todas las subidas de la entrega
    $subidas = $entrega->getSubidas();

    return $this->render('entrega/entrega.html.twig', [
        'entrega' => $entrega,
        'subidas' => $subidas,
        'formularioSubida' => $formularioSubida->createView(),
        'formularioNota' => $formularioNota->createView(),
    ]);
}



    
}

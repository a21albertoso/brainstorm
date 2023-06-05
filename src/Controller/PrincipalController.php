<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Tema;
use App\Entity\User;
use App\Entity\Entrega;
use App\Form\FotoType;
use App\Repository\EntregaRepository;
use App\Repository\SubidaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Length;
use App\Service\ColorService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;


class PrincipalController extends AbstractController{

    private $doctrine;
    private $colorService;
    private $em;
    
    public function __construct(EntityManagerInterface $em, ColorService $colorService, ManagerRegistry $doctrine)
    {
        $this->em = $em;
        $this->colorService = $colorService;
        $this->doctrine = $doctrine;
    }
    

    #[Route(path: '/', name: 'redirection')]
    public function indexCharge(){

        return $this->redirectToRoute('login');

    }
    
    #[Route(path: '/principal/{id}', name: 'principal', methods: ["GET"])]
    public function index(CacheItemPoolInterface $cache, SubidaRepository $subidaRepository, EntregaRepository $entregaRepository, Security $security, AuthenticationUtils $authenticationUtils, $id = null){

        // Obtener el usuario autenticado
        $user = $security->getUser();
        
        // Si el usuario está autenticado, obtén su ID
        $userAsignaturas = $user ? $user->getAsignaturas() : [];

        $entregasPendientes = [];
    foreach ($user->getAsignaturas() as $asignatura) {
        $entregasAsignatura = $entregaRepository->findBy(['asignatura' => $asignatura]);
        foreach ($entregasAsignatura as $entrega) {
            $subida = $subidaRepository->findSubidaByUserAndEntrega($user, $entrega);
            if (!$subida) {
                $entregasPendientes[] = $entrega;
            }
        }
    }

        $entregasPorAsignatura = [];
        foreach ($userAsignaturas as $asignatura) {
            $query = $this->em->getRepository(Entrega::class)->createQueryBuilder('e');
            $query->where('e.asignatura = :asignatura_id')
                ->setParameter('asignatura_id', $asignatura->getId());
            $entregasPorAsignatura[$asignatura->getId()] = $query->getQuery()->getResult();
        }

        

        $query = $this->em->getRepository(Asignatura::class)->createQueryBuilder('a');
        //obtener asignaturas
        $asignaturas = $query->getQuery()->getResult();

        $query2 = $this->em->getRepository(Tema::class)->createQueryBuilder('t');
        $temas = $query2->getQuery()->getResult();

        $query4 = $this->em->getRepository(Entrega::class)->createQueryBuilder('e');
        $entregaspacontar = $query4->getQuery()->getResult();


        $temasPorAsignatura = [];
        foreach ($asignaturas as $asignatura) {
            $query = $this->em->getRepository(Tema::class)->createQueryBuilder('t');
            $query->where('t.asignatura = :asignatura_id')
                ->setParameter('asignatura_id', $asignatura->getId());
            $temasPorAsignatura[$asignatura->getId()] = $query->getQuery()->getResult();
        }

        $randomColor = $this->colorService->getRandomColor();

        $numeroEntregas = count($entregasPorAsignatura); 

        //correo del usuario
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('principal/principal.html.twig',[
            'user' => $user,
            'asignaturas' => $asignaturas,
            'temas' => $temas,
            'entregas' => $entregaspacontar,
            'numeroEntregas' => $numeroEntregas,
            'last_username' => $lastUsername,
            'userAsignaturas' => $userAsignaturas,
            'entregasPorAsignatura' => $entregasPorAsignatura,
            'temasPorAsignatura' => $temasPorAsignatura,
            'randomColor' => $randomColor,
            'entregasPendientes' => $entregasPendientes,
        ]);

    }
    
    #[Route(path: '/principal/usuario', name: 'info', methods: ["GET", "POST"])]
    public function usuarioInfo(Request $request, SluggerInterface $slugger)
    {
        $randomColor = $this->colorService->getRandomColor();
    
        $user = $this->getUser(); // Obtener el usuario actualmente autenticado
        $form = $this->createForm(FotoType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $foto = $form->get('foto')->getData();
    
            if ($foto) {
                $originalFilename = pathinfo($foto->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$foto->guessExtension();
    
                try {
                    $foto->move(
                        $this->getParameter('fotos_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception("Hay un problema con tu foto de perfil");
                }
    
                $user->setFoto($newFilename);
            }
    
            $entityManager = $this->doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('info');
        }
    
        return $this->render('principal/perfil.html.twig', [
            'randomColor' => $randomColor,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/principal/usuario/erasefoto', name: 'erasefoto', methods: ['POST'])]
public function removeFoto()
{
    $user = $this->getUser();
    $user->setFoto(null);

    $entityManager = $this->doctrine->getManager();
    $entityManager->persist($user);
    $entityManager->flush();

    return $this->redirectToRoute('info');
}

    


}



?>
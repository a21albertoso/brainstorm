<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Tema;
use App\Entity\User;
use App\Entity\Entrega;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Length;
use App\Service\ColorService;


class PrincipalController extends AbstractController{

    private $em;
    private $colorService;

    public function __construct(EntityManagerInterface $em, ColorService $colorService)
    {
        $this->em = $em;
        $this->colorService = $colorService;
    }

    #[Route(path: '/', name: 'redirection')]
    public function indexCharge(){

        return $this->redirectToRoute('login');

    }
    
    #[Route(path: '/principal/{id}', name: 'principal', methods: ["GET"])]
    public function index(ColorService $colorService ,Security $security, AuthenticationUtils $authenticationUtils, $id = null){

        // Obtener el usuario autenticado
        $user = $security->getUser();
        
        // Si el usuario está autenticado, obtén su ID
        $userAsignaturas = $user ? $user->getAsignaturas() : [];

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
            'asignaturas' => $asignaturas,
            'temas' => $temas,
            'entregas' => $entregaspacontar,
            'numeroEntregas' => $numeroEntregas,
            'last_username' => $lastUsername,
            'userAsignaturas' => $userAsignaturas,
            'entregasPorAsignatura' => $entregasPorAsignatura,
            'temasPorAsignatura' => $temasPorAsignatura,
            'randomColor' => $randomColor,
        ]);

    }

    #[Route(path: '/principal/usuario', name: 'info', methods: ["GET"])]
public function usuarioInfo()
{

    $randomColor = $this->colorService->getRandomColor();

    return $this->render('principal/perfil.html.twig',[
        'randomColor' => $randomColor,
    ]);
}


}



?>
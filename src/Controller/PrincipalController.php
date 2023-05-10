<?php

namespace App\Controller;

use App\Entity\Asignatura;
use App\Entity\Tema;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PrincipalController extends AbstractController{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    } 

    #[Route(path: '/', name: 'redirection')]
    public function indexCharge(){

        return $this->redirectToRoute('app_login');

    }
    
    #[Route(path: '/principal', name: 'principal')]
    public function index(){

        $asignatura = $this->em->getRepository(Asignatura::class)->createQueryBuilder('a')
        
        ;

        $asignatura->getQuery()->getResult();

        return $this->render('principal/principal.html.twig',[
            'titulo' => 'heyheyhey',
            'asignatura' => $asignatura,
        ]);

    }

}

?>
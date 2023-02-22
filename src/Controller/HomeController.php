<?php

namespace App\Controller;

use App\Entity\Equipment;
use Doctrine\Persistence\ObjectManager;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class  HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(FlashyNotifier $flashy): Response
    {
//        $repo = $this->getDoctrine()->getRepository(Equipment::class);
//
//        $equipments = $repo->findAll();
//
//        return $this->render('home/index.html.twig', [
//            'controller_name' => 'HomeController',
//            'equipments' => $equipments
//        ]);

//        $flashy->primaryDark('Event created!', 'https://www.youtube.com/watch?v=sxLceVKcWoc');

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }
    /**
     * @Route("/home1", name="home1")
     */
    public function index1(): Response
    {
        return $this->render('front.base.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }

    /**
     * @Route("/home2", name="home2")
     */
    public function index2(): Response
    {
        return $this->render('back.base.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }

    /**
     * @Route("/home/prod", name="prod")
     */
    public function add1(Request $request){
        if ($request->isMethod('POST')){
            echo "jaw";
        }
    }
    /**
     * @Route("/home/new", name="equip_create")
     */
    public function create(){
        return $this->render('equipment/create.html.twig');
    }
}

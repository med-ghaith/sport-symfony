<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Gym;
use App\Entity\Subscription;
use App\Form\GymType;
use App\Repository\CourseRepository;
use App\Repository\GymRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
    /**
     * @Route("/mobilegym", name="app_mobilegym")
     */
class MobilegymController extends AbstractController
{

    /**
     * @Route("/getallgym", name="gym_index", methods={"GET"})
     */
    public function mobilegetallgym(GymRepository $gymRepository)
    {
        $allAppointmentsQuery=$gymRepository->findAll();
        $responses=$this->json($allAppointmentsQuery,200,[],['groups'=>['gymread']]);
            
        return $responses;
    }

    /**
     * @Route("/new", name="gym_new", methods={"GET", "POST"})
     */
    public function mobilenew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $iduser=$request->get("iduser");
        $user=$this->getDoctrine()->getRepository(User::class)->find($iduser);
        $gym = new Gym(); 
        $gym->setUser($user);
        $gym->setName($request->get("name"));
        $gym->setDescription($request->get("description"));
        $gym->setLocation($request->get("location"));
        $entityManager->persist($gym);
        $entityManager->flush();      
        return $this->json("Succes gym add",200,[]);   
    }
  

    /**
     * @Route("/showgym", name="gym_show", methods={"GET"})
     */
    public function mobileshow(CourseRepository $courseRepository,GymRepository $gymRepository,Request $request,NormalizerInterface $normalizer)
    {
        $id=$request->get("id");
        $course = $courseRepository->listcoursebygym($id);
        //$gym=$gymRepository->find($id);
        $formatted=$normalizer->normalize($course,
        null,['groups'=>['courseread']]);
        $json=json_encode($formatted);
        $response =new Response($json,200,[
            "Content-type"=>"application/json"
        ]);
            
        return $response;   
          
    }

    /**
     * @Route("/editgym", name="gym_edit", methods={"GET", "POST"})
     */
    public function mobileedit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id=$request->get("id");
        $gym=$this->getDoctrine()->getRepository(Gym::class)->find($id);
        $gym->setName($request->get("name"));
        $gym->setDescription($request->get("description"));
        $gym->setLocation($request->get("location"));
        $entityManager->persist($gym);
        $entityManager->flush();      
        return $this->json("Succes gym edit",200,[]);         
    }

    /**
     * @Route("/deletegym", name="gym_delete", methods={"POST"})
     */
    public function mobiledelete(Request $request, Gym $gym, EntityManagerInterface $entityManager): Response
    {
        $id=$request->get("id");
        $course=$this->getDoctrine()->getRepository(Gym::class)->find($id);
        $em=$this->getDoctrine()->getManager(); 
             
        if($course!=null){
            $em->remove($course);
            $em->flush();

            return $this->json("Succes gym removed",200,[]);
        }
        return $this->json("error gym removed",200,[]);
    }
}

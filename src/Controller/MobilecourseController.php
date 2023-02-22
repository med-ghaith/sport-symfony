<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Gym;

use App\Repository\CourseRepository;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

 /**
     * @Route("/mobilecourse", name="app_mobilecourse")
     */
class MobilecourseController extends AbstractController
{
   
     /**
     * @Route("/coursebygym", methods={"GET"})
     */
    public function mobileshowcoursegym(NormalizerInterface $normalizer,CourseRepository $courseRepository,Request $request)
    {  
        $id=$request->get("id");
        $course=$courseRepository->listcoursebygym($id);
        $formatted=$normalizer->normalize($course,
        null,['groups'=>['courseread']]);
        $json=json_encode($formatted);
        $response =new Response($json,200,[
            "Content-type"=>"application/json"
        ]);
            
        return $response;   
       
    }
    /**
     * @Route("/getall",  methods={"GET"})
     */
    public function mobileshowAll(CourseRepository $courseRepository,NormalizerInterface $normalizer): Response
    {  
        $course=$courseRepository->findAll();
        $formatted=$normalizer->normalize($course,
        null,['groups'=>['courseread']]);
        $json=json_encode($formatted);
        $response =new Response($json,200,[
            "Content-type"=>"application/json"
        ]);
            
        return $response;    
       
    }
    /**
     * @Route("/newcourse",  methods={"GET", "POST"})
     */
    public function mobilenew(Request $request, EntityManagerInterface $entityManager): Response
    {
        $idgym=$request->get("idgym");
        $course = new Course();
        $gym=$this->getDoctrine()->getRepository(Gym::class)->find($idgym);
        $course->setGym($gym);
        $course->setName($request->get("name"));
        $course->setDescription($request->get("description"));
        $course->setVideo($request->get("video"));
        $entityManager->persist($course);
        $entityManager->flush();
        return $this->json("Succes course add",200,[]);

    }

    /**
     * @Route("/editcourse",  methods={"GET", "POST"})
     */
    public function mobileedit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id=$request->get("id");
        $course=$this->getDoctrine()->getManager()->getRepository(Course::class)->find($id);
        $course->setName($request->get("name"));
        $course->setDescription($request->get("description"));
        $course->setVideo($request->get("video"));
        $entityManager->persist($course);
        $entityManager->flush();
        return $this->json("Succes course edit",200,[]);        
    }

    /**
     * @Route("/deletecourse", methods={"DELETE"})
     */
    public function mobiledelete(Request $request): Response
    {
        $id=$request->get("id");
        $course=$this->getDoctrine()->getRepository(Course::class)->find($id);
        $em=$this->getDoctrine()->getManager(); 
        if($course!=null){
            $em->remove($course);
            $em->flush();

            return $this->json("Succes course removed",200,[]);
        }
        return $this->json("error course removed",200,[]);
    }
}

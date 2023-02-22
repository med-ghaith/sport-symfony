<?php

namespace App\Controller;


use App\Entity\Gym;

use App\Entity\Subscription;
use App\Entity\User;

use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

   /**
     * @Route("/mobilesubscription", name="app_mobilesubscription")
     */

class MobilesubscriptionController extends AbstractController
{
 
   /**
     * @Route("/getall",name="mobilegetall", methods={"GET"})
     */
    public function getsubmobile(SubscriptionRepository $subscriptionRepository ,NormalizerInterface $normalizer): Response
    {
       
        $allAppointmentsQuery=$subscriptionRepository->findAll();
        $formatted=$normalizer->normalize($allAppointmentsQuery,
        null,['groups'=>['subread']]);
        $json=json_encode($formatted);
        $response =new Response($json,200,[
            "Content-type"=>"application/json"
        ]);
            
        return $response;
    }

    /**
     * @Route("/newsub", name="mobilenewsub",methods={"GET", "POST"})
     */
    
    public function newsubmobile(Request $request, EntityManagerInterface $entityManager): Response
    {   
        $idgym=$request->get("idgym");
        $iduser=$request->get("iduser");
        $gym=$this->getDoctrine()->getRepository(Gym::class)->find($idgym);
        $user=$this->getDoctrine()->getRepository(User::class)->find($iduser);
         
        $subscription = new Subscription();     
        $subscription->setGym($gym);  
        $subscription->setStartDate(new \DateTimeImmutable());   
        $subscription->setMember($user);
        $subscription->setValidity($request->get("validity"));            
        $entityManager->persist($subscription);
        $entityManager->flush();
        return $this->json("Succes sub add",200,[]);
    }

   

    /**
     * @Route("/editsub",name="mobileedit", methods={"GET", "POST"})
     */
    public function editsubmobile(Request $request): Response
    { 
        $id=$request->get("id");
        $em=$this->getDoctrine()->getManager();
        $sub=$this->getDoctrine()->getManager()->getRepository(Subscription::class)->find($id);
        $sub->setValidity($request->get("validity"));
        $em->persist($sub);
        $em->flush();
        return $this->json("Succes sub edit",200,[]);
    }

    /**
     * @Route("/deletesub",name="mobiledelete", methods={"DELETE"})
     */
    public function deletesubmobile(Request $request): Response
    {
       $id=$request->get("id");
        $sub=$this->getDoctrine()->getRepository(Subscription::class)->find($id);
        $em=$this->getDoctrine()->getManager(); 
        if($sub!=null){
            $em->remove($sub);
            $em->flush();

            return $this->json("Succes sub removed",200,[]);
        }
        return $this->json("error sub removed",200,[]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api_events", name="api_")
 */
class APIController extends AbstractController
{
    /**
     *
     * @Route("/events/liste", name="liste")
     */
    public function liste(EventRepository $repository ,SerializerInterface $serializer)
    {
        $events=$repository->findAll();
//        $encoders=[new JsonEncoder()];
//        $normalizer =[new ObjectNormalizer()];
//        $serializer=new \Symfony\Component\Serializer\Serializer($normalizer,$encoders);
        $jsonContent=$serializer->serialize($events,'json',['groups' => 'event']

        );
        $response=new Response($jsonContent);
        $response->headers->set('Content-Type','application/json');
        return $response;
    }
    /**
     *
     * @Route("/events/affiche/{id}", name="affiche", methods={"GET"})
     */
    public function getEvent(Event $event ,SerializerInterface $serializer)
    {
        $encoders=[new JsonEncoder()];
        $normalizer =[new ObjectNormalizer()];
        //  $serializer=new \Symfony\Component\Serializer\Serializer($normalizer,$encoders);
        $jsonContent=$serializer->serialize($event,'json',['groups' => 'event']
        );
        $response=new Response($jsonContent);
        $response->headers->set('Content-Type','application/json');
        return $response;
    }
    /**
     * @Route("/event/ajout", name="ajout", methods={"POST"})
     */
    public function addEvent(Request $request, SerializerInterface $serializer)
    {
        $em=$this->getDoctrine()->getManager();
        $e=new Event();
        $donnees = json_decode($request->getContent());
        $e->setCategory($request->get('category'));
        $e->setImageUrl($request->get('imageUrl'));
        $e->setFees($request->get('fees'));
        $e->setNombreReservation($request->get('nombreReservation'));
        $e->setDescription($request->get('category'));
        $e->setPlanning($request->get('planning'));

        $e->setStartDate(new \DateTime($request->get('startDate')));
        $e->setEndDate(new \DateTime($request->get('endDate')));

//        $e->setCategory($donnees->category);
//        $e->setDescription($donnees->description);
//       // $e->setPlanning($donnees->planning);
//        $e->setNombreReservation($donnees->nombreReservation);
//        $e->setFees($donnees->fees);
//        $e->setImageUrl($donnees->imageUrl);
//        $e->setStartDate(new \DateTime($donnees->startDate));
//        $e->setEndDate(new \DateTime($donnees->endDate));
        //dd($e);
        $em->persist($e);
        $em->flush();
        $jsonContent=$serializer->serialize($e,'json',['groups'=>'event']);
        return new Response(json_encode($jsonContent));
    }
    /**
     * @Route("/event/delete/{id}", name="evenement_delete_api")
     */
    public function deleteEvent(Request $request, $id,SerializerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $event=$em->getRepository(Event::class)->find($id);
        $em->remove($event);
        $em->flush();
        $jsonContent=$normalizer->serialize($event,'json',['groups'=>'event']);
        return new Response("Deleted successfully".json_encode($jsonContent));
    }
    /**
     * @Route("/event/edit/{id}", name="evenement_edit_api")
     */
    public function editEvent(Request $request, $id,SerializerInterface $normalizer)
    {
        $em=$this->getDoctrine()->getManager();
        $e=$em->getRepository(Event::class)->find($id);
    /*    $e->setNameEvent($request->get('NameEvent'));
        $e->setPlaceEvent($request->get('PlaceEvent'));
        $e->setPriceEvent($request->get('PriceEvent'));
        $e->setNbParticipants($request->get('NbParticipants'));
        $e->setDateDebut(new \DateTime($request->get('DateDebut')));
        $e->setDateFin(new \DateTime($request->get('DateFin')));*/

        $e->setCategory($request->get('category'));
        $e->setImageUrl($request->get('imageUrl'));
        $e->setFees($request->get('fees'));
        $e->setNombreReservation($request->get('nombreReservation'));
        $e->setDescription($request->get('category'));
      //  $e->setPlanning($request->get('planning'));

        $e->setStartDate(new \DateTime($request->get('startDate')));
        $e->setEndDate(new \DateTime($request->get('endDate')));
        $em->flush();
        $jsonContent=$normalizer->normalize($e,'json',['groups'=>'post:read']);
        return new Response("update successfully".json_encode($jsonContent));


    }

/**
 * @Route("/event/apiSortPrice", name="evenement_sortprice_api")
 */
public function sortjsonPrice(SerializerInterface $Normalizer) : Response
{
    $evenements=$this->getDoctrine()->getRepository(Event::class)->SortByPriceEvent();
    $jsonContent=$Normalizer->serialize($evenements,'json',['groups'=>'event']);
    $response=new Response($jsonContent);
    $response->headers->set('Content-Type','application/json');
    return $response;
}
/**
 * @Route("/event/apiSortPart", name="evenement_sortpart_api")
 */
public function sortjsonPart(SerializerInterface $Normalizer) : Response
{
    $evenements=$this->getDoctrine()->getRepository(Event::class)->SortByParticipants();
    $jsonContent=$Normalizer->serialize($evenements,'json',['groups'=>'event']);
    $response=new Response($jsonContent);
    $response->headers->set('Content-Type','application/json');
    return $response;}
/**
 * @Route("/event/apiSortName", name="evenement_sortname_api")
 */
public function sortjsonName(SerializerInterface $Normalizer) : Response
{
    $evenements=$this->getDoctrine()->getRepository(Event::class)->SortByParticipants();
    $jsonContent=$Normalizer->serialize($evenements,'json',['groups'=>'event']);
    $response=new Response($jsonContent);
    $response->headers->set('Content-Type','application/json');
    return $response;}
}

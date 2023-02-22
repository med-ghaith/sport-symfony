<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Planning;
use App\Form\EventType;
use App\Repository\EventRepository;
use ContainerDrqUxNx\PaginatorInterface_82dac15;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
//    /**
//     * @Route ("/recherche",name="event_recherche")
//     */
//    function recherche(EventRepository $repository, Request $request)
//    {
//        $data = $request->get('recherche');
//
//        $event = $repository->findBy(['category' => $data]);
//
//        return $this->render('event/event_list_front.html.twig', [
//            'events' => $event,
//        ]);
//    }
    /**
     * @Route("/listevents", name="event_index", methods={"GET"})
     */
    public function index(EventRepository $eventRepository ,Request $request,PaginatorInterface $paginator): Response

    {
        $donnees=$eventRepository->findAll();
        $articles = $paginator->paginate(
        $donnees, // Requête contenant les données à paginer (ici nos articles)
        $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
        3 );


        return $this->render('event/index.html.twig', [
            'events' =>$articles ,
        ]);
    }

    /**
     * @Route("/calendrier", name="calendar", methods={"GET"})
     */
    public function calendar(EventRepository $eventRepository)
    {

        $evs = $eventRepository->findAll();
        $rdvs = [];
        foreach ($evs as $ev) {
            $rdvs[] = [
                'id' => $ev->getId(),
                'title' => $ev->getCategory(),
                'description' => $ev->getDescription(),
                'start' => $ev->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $ev->getEndDate()->format('Y-m-d H:i:s'),

            ];

        }
        //dd($rdvs);
        $data = json_encode($rdvs);
        return $this->render('event/test.html.twig', compact('data'));

    }




    /**
     * @Route("/eventsGrid", name="event_index_grid", methods={"GET"})
     */
    public function indexGrid(EventRepository $eventRepository): Response
    {
        return $this->render('event/event_grid.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    /**
     * @Route("/filter", name="filter", methods={"GET","POST"})
     */
    public function Filter(Request $request, EventRepository $eventRepository)
    {$events=$eventRepository->findAll();
        if ($request->isMethod("POST")) {
            if ($request->request->get('optionsRadios')) {
                $SortKey = $request->request->get('optionsRadios');
                switch ($SortKey) {
                    case 'NameEvent':
                        $events = $eventRepository->SortByNameEvent();
                        break;

                    case 'PriceEvent':
                        $events = $eventRepository->SortByPriceEvent();
                        break;

                    case 'NbParticipants':
                        $events = $eventRepository->SortByParticipants();
                        break;

                }
            }
//            else
//            {
//                $type = $request->request->get('optionsearch');
//                $value = $request->request->get('Search');
//                switch ($type){
//                    case 'NameEvent':
//                        $events = $evenementRepository->findByNameEvent($value);
//                        break;
//
//                    case 'PlaceEvent':
//                        $events = $evenementRepository->findByPlaceEvent($value);
//                        break;
//
//                    case 'DateDebut':
//                        $events = $evenementRepository->findByDateDebut($value);
//                        break;
//
//                    case 'DateFin':
//                        $events = $evenementRepository->findByDateFin($value);
//                        break;
//
//
//                }
//            }
//            if ( $events){
//                $back = "success";
//            }else{
//                $back = "failure";
//            }
//            //dd($request->request->get('optionsRadios'));
//            $evenements=$paginator->paginate(
//                $events,
//                $request->query->getInt('page',1),
//                3);

            return $this->render('event/event_list_front.html.twig', [
                'events' => $events,
            ]);
        }
        return $this->render('event/event_list_front.html.twig', [
            'events' => $events,
        ]);
    }
    /**
     * @Route("/filterback", name="filter_back", methods={"GET","POST"})
     */
public function filerback(EventRepository $eventRepository ,Request $request,PaginatorInterface $paginator){
    $donnees=$eventRepository->findAll();
    $articles = $paginator->paginate(
        $donnees, // Requête contenant les données à paginer (ici nos articles)
        $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
        3 );
    $back = null;
    if($request->isMethod("POST")){
        if ( $request->request->get('optionsRadios')){
            $SortKey = $request->request->get('optionsRadios');
            switch ($SortKey){
                case 'NameEvent':
                    $events = $eventRepository->SortByNameEvent();
                    break;

                case 'PriceEvent':
                    $events = $eventRepository->SortByPriceEvent();
                    break;

                case 'NbParticipants':
                    $events = $eventRepository->SortByParticipants();
                    break;

            }
        }
        else
        {
            $type = $request->request->get('optionsearch');
            $value = $request->request->get('Search');
            switch ($type){
                case 'NameEvent':
                    $events = $eventRepository->findByNameEvent($value);
                    break;


                case 'DateDebut':
                    $events = $eventRepository->findByDateDebut($value);
                    break;

                case 'DateFin':
                    $events = $eventRepository->findByDateFin($value);
                    break;


            }
        }
        if ( $events){
            $back = "success";
        }else{
            $back = "failure";
        }
        //dd($request->request->get('optionsRadios'));
        $articles=$paginator->paginate(
            $events,
            $request->query->getInt('page',1),
            3);

        return $this->render('event/index.html.twig',array('events'=>$articles, 'back'=>$back));

    }

}

    /**
     * @Route("/events_row", name="event_index_row", methods={"GET"})
     */
    public function indexrow(EventRepository $eventRepository, Request $request): Response
    {
        $events = $eventRepository->findAll();

            return $this->render('event/event_list_front.html.twig', [
                'events' => $events,
            ]);
        }

    /**
     * @Route("/{id}/events_row_planning", name="event_index_row_planning", methods={"GET"})
     */
    public function indexrowPlanning(EventRepository $eventRepository ,int $id): Response
    {
       // $planning=$this->getDoctrine()->getRepository(Planning::class)->find($id);

        return $this->render('event/event_list_front.html.twig', [
            'events' => $eventRepository->getEventByPlanning($id)
        ]);
    }


    /**
     * @Route("/newEvent", name="new_event", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['imageFile']->getData();

                $newFilename =  ' http://localhost/sports-hub-images/'.$uploadedFile->getClientOriginalName();
                $uploadedFile->move(
                    $this->getParameter('upload_directory')
            ,
                    $newFilename
                );
                $event->setImageUrl($newFilename);

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event_index_grid', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/newEventFront.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/newEventFront", name="new_event_front", methods={"GET", "POST"})
     */
    public function newFront(Request $request, EntityManagerInterface $entityManager): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['imageFile']->getData();

            $newFilename =  ' http://localhost/sports-hub-images/'.$uploadedFile->getClientOriginalName();
            $uploadedFile->move(
                $this->getParameter('upload_directory')
                ,
                $newFilename
            );
            $event->setImageUrl($newFilename);
            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event_index_grid', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/newEventFront.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_show", methods={"GET"})
     */
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }
    /**
     * @Route("/eventfront/{id}", name="event_show_front", methods={"GET"})
     */
    public function showFront(Event $event): Response
    {
        return $this->render('event/showFront.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="event_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/editFront", name="event_edit_front", methods={"GET", "POST"})
     */
    public function editFront(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('event_index_row', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event/editFront.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="event_delete", methods={"POST"})
     */
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("deleteFront/{id}", name="event_delete_front", methods={"POST"})
     */
    public function deleteFront(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index_row', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/event/stats", name="stats_event")
     */
    public function statistiques(EventRepository $eventRepository)
    { $event = $eventRepository->findAll();
        /* you can also inject "FooRepository $repository" using autowire */

        /* $count = $repository->count();
         dd($count); */

        /*  $countbydate= $repository->createQueryBuilder('a')
         ->select('SUBSTRING(datefin,1,10) As datedufin, COUNT(a) as count')
         ->groupby('datedufin')
         ->getQuery()
         ->getResult(); */
        //
        $repository = $this->getDoctrine()->getRepository(Event::class);
        $count= $repository->createQueryBuilder('u')
            ->select('count(u.category)')
            ->groupby('u.category')
            ->getQuery()
            ->getResult();

        $countdate= $repository->createQueryBuilder('a')
            ->select('(a.category)')
            ->groupby('a.category')
            ->getQuery()
            ->getResult();
        foreach($event as $event){

            $date[] = $event->getCategory();

        }


        for ($i = 0; $i < count($count); ++$i){

            $count1[] = $count[$i][1] ;
            $countdate1[] = $countdate[$i][1];
        }


        return $this->render('event/stats.html.twig', [
            'date' => json_encode($date ),
            'count1' => json_encode($count1),
            'countdate1' => json_encode($countdate1),



        ]);
    }


}

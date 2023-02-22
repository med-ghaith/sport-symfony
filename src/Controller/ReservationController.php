<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Entity\User;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Mediumart\Orange\SMS\SMS;
use Mediumart\Orange\SMS\Http\SMSClient;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/", name="reservation_index", methods={"GET"})
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    /**
     * @Route("/pdf", name="pdf", methods={"GET"})
     */
    public function pdf(ReservationRepository $reservationRepository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $reclamation = $reservationRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("Reclamation.pdf", [
            "Attachment" => false
        ]);


    }

    /**
     * @Route("/{id}/new", name="reservation_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager ,int $id ,\Swift_Mailer $mailer): Response
    {
        $reservation = new Reservation();
//        $user = $this->getDoctrine()->getRepository(User::class)->find(30);
        $user=$this->getUser();
        //$mail=$user->get();
        $event=$this->getDoctrine()->getRepository(Event::class)->find($id);

        $res=$this->getDoctrine()->getRepository(Reservation::class)->findBy(['user' => $user,
        'event'=>$event]);
        $form = $this->createForm(ReservationType::class, $reservation);
        if($user==null){
            $request->getSession()
                ->getFlashBag()
                ->add('error', "You need to be logged in to make a reservation");
            return $this->redirectToRoute('event_show_front',['id' => $id]);
        }
        else if($res== true){
            $request->getSession()
                ->getFlashBag()
                ->add('error', "This reservation already exists");
            return $this->redirectToRoute('event_show_front',['id' => $id]);
        }
        else {
            $form->handleRequest($request);
            $reservation->setUser($user);
            $reservation->setEvent($event);
            $reservation->setStatus("reserved");
            $message = (new \Swift_Message("nouvelle reservation"))
                ->setFrom('hamatalbi9921@gmail.com')
                ->setTo('mohamedali.benfarah@esprit.tn')
                ->setBody(
                    'Client'.' :'.$this->getUser()->getUsername().'  has made a reservation to an event with id : '.$event

                );
            $mailer->send($message);
//$this->addFlash('message','tb3ath');
            $request->getSession()
                ->getFlashBag()
                ->add('error', "Your Reservation was maid succesfully please check your email for confirmation");
//            $client = SMSClient::getInstance('E4YkyQaxGlbhJZsVgMARqyOOaZ5HCbPe', 'DSM1wWZCjRt05ymB');
//            $sms = new SMS($client);
//            $sms->message('Hello, my dear...')
//                ->from('+21622218580')
//                ->to('+21629883182')
//                ->send();
            $client = SMSClient::getInstance('nEoxkRRL52MtHzUNAoaXc0ngnNVl9KDC', 'zSB1YIu2CSwoLnBL');
            $sms = new SMS($client);
            $sms->message('Your reservation was made succesfully for event '.$reservation->getEvent().'By User '.$reservation->getUser()->getUsername())
                ->from('+21654302753')
                ->to('+21622218580')
                ->send();
            $entityManager->persist($reservation);
            $entityManager->flush();


        }
        return $this->redirectToRoute('event_show_front',['id' => $id]);

    }

    /**
     * @Route("/{id}", name="reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservation_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reservation_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/res/apiDisplay", name="reservation_index2_api")
     */
    public function indexfrontjson(Request $request,SerializerInterface $Normalizer)
    {

        $reservations=$this->getDoctrine()->getRepository(Reservation::class)->findAll();
        $jsonContent=$Normalizer->serialize($reservations,'json',['groups'=>'reservation']);
        $response=new Response($jsonContent);
        $response->headers->set('Content-Type','application/json');
        return $response;}

    /**
     * @Route("/res/{id}/{idu}", name="reservation_new_front_api")
     */
    public function new_frontjson(Request $request, EntityManagerInterface $entityManager,SerializerInterface $Normalizer,$id,$idu): Response
    {
       // $DateReservation = date('d-m-y , h:m');
        // $user=$this->getUser();
        $user=$this->getDoctrine()->getRepository(User::class)->findOneBy(['id'=>$idu]);
        $evenement=$this->getDoctrine()->getRepository(Event::class)->findOneBy(['id'=>$id]);
   //     $evenement->setNombreReservation($evenement->getNombreReservation()+1);
       // $idEvent=$evenement->get();
        $reservation = new Reservation();
      //  $reservation->setDateReservation(new \DateTime('now'));
      //  $ev=$evenement->setNameEvent($idEvent);
        $reservation->setEvent($evenement);
      //  $DateReservation=$reservation->getDateReservation();
        $reservation->setStatus("reserved");
        $reservation->setUser($user);
        //dd($reservation);
        $entityManager->persist($reservation);
        $entityManager->flush();
        $jsonContent=$Normalizer->serialize($reservation,'json',['groups'=>'reservation']);
        //dd($jsonContent);
        return new Response("reservation added successfully".json_encode($jsonContent));
    }
    /**
     * @Route("/reservation/delete/{idR}", name="reservation_delete_api")
     */
    public function deletejson(ReservationRepository $repo,$idR,SerializerInterface $Normalizer)
    {
        $res = $repo->find($idR);
        $em = $this->getDoctrine()->getManager();
        $em->remove($res);
        $em->flush();
        $jsonContent=$Normalizer->serialize($em,'json',['groups'=>'reservation']);
        return new Response("reservation deleted successfully".json_encode($jsonContent));


    }
}

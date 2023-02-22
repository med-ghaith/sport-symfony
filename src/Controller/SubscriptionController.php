<?php

namespace App\Controller;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use App\Entity\Gym;
use App\Entity\Payment;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\PaymentType;
use App\Form\SubscriptionType;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\QrcodeService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/subscription")
 */
class SubscriptionController extends AbstractController
{
    /**
     * @Route("/", name="subscription_index", methods={"GET"})
     */
    public function index(SubscriptionRepository $subscriptionRepository): Response
    {
        return $this->render('subscription/index.html.twig', [
            'subscriptions' => $subscriptionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="subscription_new", methods={"GET", "POST"})
     * @param QrcodeService $qrcodeService
     */
    
    public function new(Request $request, EntityManagerInterface $entityManager,QrcodeService $qrcodeService): Response
    {   
        $gym=$this->getDoctrine()->getRepository(Gym::class)->find($request->query->get("idgym"));
        $user=$this->getDoctrine()->getRepository(User::class)->find($request->query->get("iduser"));
         
        $subscription = new Subscription();
        $payment=new Payment();
        $form = $this->createForm(SubscriptionType::class, $subscription);
        $modepayment = $this->createForm(PaymentType::class,$payment);
        $form->handleRequest($request);
        $modepayment->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subscription->setGym($gym);  
            $subscription->setStartDate(new \DateTimeImmutable());   
            $subscription->setMember($user); 
            $prix = (int)substr($subscription->getValidity(),0,2)*90;
            $entityManager->persist($subscription);
            $entityManager->flush();
            $entityManager->refresh($subscription);
            if($payment->getMethode()){
                return $this->redirectToRoute('payment',[
                'subscription'=>$subscription->getId(),
                'prix'=>$prix]);
            }else{
            $qrCode = $qrcodeService->qrcode("[Sportshub: ".$gym->getName().
            " payment sur place ".$subscription->getValidity()
            ."] Date".date_format($subscription->getStartDate(), 'Y-m-d H:i:s').
            "name: ".$user->getlastName()." ".$user->getfirstName().
            " email: ".$user->getEmail().
            " phone: ");

            return $this->render('subscription/Facture.html.twig', ['id'=>$request->query->get("idgym"),
                    'gym'=>$gym,'user'=>$user,
                    'subscription'=>$subscription,
                    'prix'=>$prix,
                    'qrCode' => $qrCode]);
                }
        }

        return $this->render('subscription/new.html.twig', [
            'subscription' => $subscription,
            'form' => $form->createView(),
            'modepayment'=>$modepayment->createView(),
        ]);
    }

     /**
     * @Route("/payment", name="payment", methods={"GET", "POST"})
     */
    public function payment(Request $request): Response
    {           
        $privateKey=null;
        if($_ENV['APP_ENV']  === 'dev') {
            $privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else {
            $privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
        }
        Stripe::setApiKey($privateKey);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items'           => [
                [
                    'price_data' => [
                        'currency'     => 'eur',
                        'product_data' => [
                            'name' => 'Abonnement',
                        ],
                        'unit_amount'  => $request->query->get('prix')*100,
                    ],
                    'quantity'   => 1,
                ]
            ],
            'mode'                 => 'payment',
            'success_url'          => $this->generateUrl('success_url', ['succes'=>$request->query->get('subscription')], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url'           => $this->generateUrl('cancel_url', ['succes'=>$request->query->get('subscription')], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);

        return $this->redirect($session->url, 303);
    }
    /**
     * @Route("/success-url", name="success_url", methods={"GET", "POST"})
     * @param QrcodeService $qrcodeService
     */
    public function successUrl(Request $request,QrcodeService $qrcodeService): Response
    {  
        $subscription=$this->getDoctrine()->getRepository(Subscription::class)->find($request->query->get("succes"));
        $gym=$this->getDoctrine()->getRepository(Gym::class)->find($subscription->getGym()->getId());
        $user=$subscription->getMember();
        $qrCode = $qrcodeService->qrcode("[Sportshub: ".$gym->getName().
        " payment en ligne ".$subscription->getValidity()
        ."] Date".date_format($subscription->getStartDate(), 'Y-m-d H:i:s').
        "name: ".$user->getlastName()." ".$user->getfirstName().
        " email: ".$user->getEmail().
        " phone: ");
        $prix = (int)substr($subscription->getValidity(),0,2)*90;
        return $this->render('subscription/Facture.html.twig', ['id'=>$request->query->get("idgym"),
                'gym'=>$gym,'user'=>$subscription->getMember(),
                'subscription'=>$subscription,
                'prix'=>$prix,
                'qrCode' => $qrCode]);
            
    }

    /**
     * @Route("/cancel-url", name="cancel_url", methods={"GET", "POST"})
     */
    public function cancelUrl(Request $request): Response
    {
        $subscription=$this->getDoctrine()->getRepository(Subscription::class)->find($request->query->get("succes"));
        return $this->redirectToRoute('subscription_delete', ['id'=>$request->query->get("succes"),
            'idgym'=>$subscription->getGym()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/edit", name="subscription_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Subscription $subscription, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SubscriptionType::class, $subscription);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('subscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('subscription/edit.html.twig', [
            'subscription' => $subscription,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{idgym}", name="subscription_delete", methods={"GET"})
     */
    public function delete(SubscriptionRepository $subscriptionRepository,$id,$idgym, EntityManagerInterface $entityManager): Response
    {
        $em=$this->getDoctrine()->getRepository(Gym::class);
        $c=$em->find($idgym);
        $em=$this->getDoctrine()->getRepository(Subscription::class);
        $sub=$em->find($id);//pour avoir tout l'objet
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($sub);
        $entityManager->flush();
        return $this->redirectToRoute('gym_show', ['id'=>$c->getid()], Response::HTTP_SEE_OTHER);
    }
}

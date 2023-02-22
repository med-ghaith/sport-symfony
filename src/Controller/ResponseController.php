<?php

namespace App\Controller;
use App\Entity\Response as Res;
use App\Form\ResponseType;
use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use App\Repository\ResponseRepository;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ResponsType;
use Mediumart\Orange\SMS\SMS;
use Mediumart\Orange\SMS\Http\SMSClient;

class ResponseController extends AbstractController
{
    /**
     * @Route("/response", name="response")
     */
    public function index(): Response
    {
        return $this->render('response/index.html.twig', [
            'controller_name' => 'ResponseController',
        ]);
    }

     /**
     * @Route("/reponse/add/{id}", name="rep_add")
     */
    public function addResponse (Reclamation $recl,Reclamation $subj,Request $req, ReclamationRepository $rep, $id,SessionInterface $session , \Swift_Mailer $mailer)
    {   
        $reclamation = $session->get("reclamation");
        


        $idReclamation=$rep->find($id); 
        $em=$this->getDoctrine()->getManager();
        $reponses= new Res();
        
        $recl->setStatus('Treated');
        $form=$this->createForm(ResponsType::class);
        
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid())
        { 
            $response=$form->getData();
            $message = (new \Swift_Message('Response'))
                 ->setFrom('hamatalbi9921@gmail.com')
                 ->setTo($idReclamation->getUser()->getEmail())
                ->setBody(
                     $this->renderView(
                        'response/addResponse.html.twig',
                        compact('response')
                     ),
                    'text/html'

            );
           // dd($response["object"]);
            $mailer->send($message);
            $reponses = $reponses->setReclamation($idReclamation);
            
            $reponses = $reponses->setContent($response["Response"]);
            $reponses = $reponses->setObject($response["object"]);
            $em=$this->getDoctrine()->getManager();
            $em->persist($reponses);
            $em->flush();


            $client = SMSClient::getInstance('nEoxkRRL52MtHzUNAoaXc0ngnNVl9KDC', 'zSB1YIu2CSwoLnBL');
                $sms = new SMS($client);
                $sms->message('Your Reclamation is treated succesfully '.$reponses->getContent().'
'.$reponses->getObject())
                    ->from('+21654302753')
                    ->to('+21654302753')
                    ->send();

            $this->addFlash('success', 'Your Response is added successfully');
            return $this->redirectToRoute('reponse_list');

        }

        return $this->render('response/index.html.twig', [
            'formA'=>$form->createView(), 
            'reclamation' => $reclamation,
            
            
        ]);
    }

    /**
     * @param ResponseRepository $rep
     * @return Res
     * @Route("response/list", name="reponse_list")
     */
    public function afficher_reponses(ResponseRepository $rep): Response
    {
        $reponses=$rep->findAll();
        return $this->render('response/listResponses.html.twig', [
            'tab1' => $reponses,
        ]);
    }

     /**
     * @return Reponse
     * @Route("/response/delete/{id}", name="response_delete")
     */

    public function Delete_reponse($id, ResponseRepository $rep){
        $response=$rep->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($response);
        $em->flush();

        return $this->redirectToRoute('reponse_list');
    }

    /**
     * @Route("reponse/update/{id}", name="reponse_update")
     */
    public function update_reponse(Request $request, $id, ResponseRepository $rep)
    {
       
        $reponse=$rep->find($id);

        $form=$this->createForm(ResponseType::class,$reponse);
       
        $form->add('update', SubmitType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'Your Response is added successfully');

            return $this->redirectToRoute('reponse_list');
        }
        return $this->render('response/update.html.twig', [
            'formA'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/reponse/treat/{id}", name="rep_treat")
     */
    public function treat (Reclamation $recl,Reclamation $subj,Request $req, ReclamationRepository $rep, $id,SessionInterface $session ,\Swift_Mailer $mailer)
    {   
        
        


        $idReclamation=$rep->find($id); 
        $em=$this->getDoctrine()->getManager();
        
        
        $idReclamation->setStatus('Treated');

        $em->persist($idReclamation);
        $em->flush();
        $message = (new \Swift_Message('Response'))
                 ->setFrom('hamatalbi9921@gmail.com')
                 ->setTo($idReclamation->getUser()->getEmail())
                ->setBody(
                     'your reclamation is treated'

            );
           // dd($response["object"]);
            $mailer->send($message);
                   

          
           // dd($response["object"]);
           
      //  $json = $serializerInterface->serialize($reclamation, 'json', ['groups' => 'rec']);
        return new Response('reclamation treated succefully');
        
    }


}

<?php

namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ReclamationType;
use App\Form\ResponsType;
use App\Repository\ReclamationRepository;
use App\Entity\Reclamation;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Knp\Component\Pager\PaginatorInterface;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;





class ReclamationController extends AbstractController
{
    /**
     * @Route("/reclamation", name="reclamation")
     */
    public function index(): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
        ]);
    }

     /**
     * @Route("/listReclamation", name="listReclamation")
     */
    public function listReclamation(Request $request,PaginatorInterface $paginator): Response
    {
        $reclamations = $this->getDoctrine()->getRepository(Reclamation::class)->findAll();
        $reclamations = $paginator->paginate(
            $reclamations, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
        5 /*limit per page*/
        );
        return $this->render('reclamation/list.html.twig', ["reclamations" => $reclamations]);
    }

    /**
     * @Route("/addReclamation", name="addReclamation")
     */
    public function addReclamation(Request $request)
    {
        $reclamation = new Reclamation();
        $reclamation->setStatus("Pending");
        $user = $this->getDoctrine()->getRepository(User::class)->find(38);
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->add("Add", SubmitType::class);
        $form->handleRequest($request);
        $reclamation->setUser($user);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($reclamation);
            $em->flush();
            return $this->redirectToRoute('listReclamation');
        }
        return $this->render("reclamation/addRec.html.twig", array('form' => $form->createView()));
    }

    /**
     * @Route("/delete/{id}", name="deleteReclamation")
     */
    public function deleteReclamation($id)
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($reclamation);
        $em->flush();
        return $this->redirectToRoute("listReclamation");
    }

    /**
     * @Route("/update/{id}", name="updateReclamation")
     */
    public function updateReclamation(Request $request, $id)
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->add("Modify", SubmitType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('listReclamation');
        }
        return $this->render("reclamation/update.html.twig", array('form' => $form->createView()));
    }

     /**
      * @Route("/show/{id}", name="showReclamation")
      */
     public function showReclamation($id)
     {
         $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        return $this->render('reclamation/show.html.twig', array("reclamation" => $reclamation));
     }

    /**
     * @Route("/treat/{id}", name="treatReclamation")
     */
    public function treatReclamation($id, Request $request, \Swift_Mailer $mailer)
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);
        $form = $this->createForm(ResponsType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $reclamation->setStatus("Treated");
            $response=$form->getData();
            $message = (new \Swift_Message('Response'))
                 ->setFrom('hamatalbi9921@gmail.com')
                 ->setTo($reclamation->getUser()->getEmail())
                ->setBody(
                     $this->renderView(
                        'emails/Response.html.twig',
                        compact('response')
                     ),
                    'text/html'

            );
            $em = $this->getDoctrine()->getManager();
            $em->persist($reclamation);
            $em->flush();
        $mailer->send($message);



        $this->addFlash('message', 'Reclamation treated successfully');
        return $this->redirectToRoute('listReclamation');

        }
        return $this->render('reclamation/show.html.twig', array("reclamation" => $reclamation, 'form'=> $form->createView()));
    }

    /**
     * @Route("/pdf", name="pdf")
     */
    public function pdfgenrator(ReclamationRepository $ReclamationRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $reclamation = $ReclamationRepository->findAll();

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reclamation\pdf.html.twig', [
            'reclamation' => $reclamation,
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
     * @Route("admin/reclamation/stats", name="stats")
     */
    public function statistiques(ReclamationRepository $reclamationRepository)
    { $reclamation = $reclamationRepository->findAll();
        /* you can also inject "FooRepository $repository" using autowire */

       /* $count = $repository->count();
        dd($count); */

           /*  $countbydate= $repository->createQueryBuilder('a')
            ->select('SUBSTRING(datefin,1,10) As datedufin, COUNT(a) as count')
            ->groupby('datedufin')
            ->getQuery()
            ->getResult(); */
       //
       $repository = $this->getDoctrine()->getRepository(Reclamation::class);
       $count= $repository->createQueryBuilder('u')
            ->select('count(u.typeReclamation)')
            ->groupby('u.typeReclamation')
            ->getQuery()
            ->getResult();

            $countdate= $repository->createQueryBuilder('a')
            ->select('(a.typeReclamation)')
            ->groupby('a.typeReclamation')
            ->getQuery()
            ->getResult();
        foreach($reclamation as $reclamation){

            $date[] = $reclamation->getTypeReclamation();

        }


            for ($i = 0; $i < count($count); ++$i){

                $count1[] = $count[$i][1] ;
                $countdate1[] = $countdate[$i][1];
            }


        return $this->render('reclamation/stats.html.twig', [
            'date' => json_encode($date ),
            'count1' => json_encode($count1),
            'countdate1' => json_encode($countdate1),
            


        ]);
    }


    /**
 * @Route("rec/api/liste", name="liste-rec", methods={"GET"})
 */
public function liste(ReclamationRepository $recRepo, NormalizerInterface $normalizer,SerializerInterface $serializerInterface)
{
    // On récupère la liste des articles
    $reclamations = $recRepo->findAll();

   $json= $serializerInterface->serialize($reclamations, 'json', ['groups' => 'rec']);
   
   $response = new Response($json, 200, [
       "Content_Type" => "application/json"
   ]);
   return $response;

}

 /**
 * @Route("rec/api/det/{id}", name="det-rec", methods={"GET"})
 */
public function recdet(ReclamationRepository $recRepo, NormalizerInterface $normalizer,SerializerInterface $serializerInterface, $id)
{
    // On récupère la liste des articles
    $reclamations = $this->getDoctrine()->getRepository(Reclamation::class)->find($id);

   $json= $serializerInterface->serialize($reclamations, 'json', ['groups' => 'rec']);
   
   $response = new Response($json, 200, [
       "Content_Type" => "application/json"
   ]);
   return $response;

}

    
    

    /**
     * @param $id
     * @Route("/deleterecApi/{id}", name="deleterecApi")
     * methods={"GET"}
     */
    public function deleteRecApi(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $rec = $em->getRepository(Reclamation::class)->find($id);
        if ($rec) {
            $em->remove($rec);
            $em->flush();
            return new JsonResponse('Deleted', 200);
        }
        return new JsonResponse('Error not found', 500);
    }


    


    /**
     * @Route("/editr/{id}", name="addrecj")
     * * methods={"POST"}
     */
    public function editjson(Request $req, SerializerInterface $serializerInterface, EntityManagerInterface $em, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->find($id);
        $reclamation->setObject($req->get('object'));
        $reclamation->setDescription($req->get('description'));
        $reclamation->setTypeReclamation($req->get('typeReclamation'));
        

        $em->persist($reclamation);
        $em->flush();
        $json = $serializerInterface->serialize($reclamation, 'json', ['groups' => 'rec']);
        return new Response('reclamation updated succefully');
    }

    /**
     * @Route("/new", name="addrecj")
     * * methods={"POST"}
     */
    public function addjson(Request $req, SerializerInterface $serializerInterface, EntityManagerInterface $em)
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = new Reclamation();
        $reclamation->setObject($req->get('object'));
        $reclamation->setDescription($req->get('description'));
        $reclamation->setTypeReclamation($req->get('typeReclamation'));
        $reclamation->setStatus("Pending");
        
        $user = $this->getDoctrine()->getRepository(User::class)->find(38);
        $reclamation->setUser($user);
        

        $em->persist($reclamation);
        $em->flush();
      //  $json = $serializerInterface->serialize($reclamation, 'json', ['groups' => 'rec']);
        return new Response('reclamation aded succefully');
    }


}

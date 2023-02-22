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
use Knp\Component\Pager\PaginatorInterface;

use function PHPUnit\Framework\equalTo;

/**
 * @Route("/gym")
 */
class GymController extends AbstractController
{
    /**
     * @Route("/", name="gym_index", methods={"GET"})
     */
    public function index(Request $request,GymRepository $gymRepository,PaginatorInterface $paginator): Response
    {
        $allAppointmentsQuery=$gymRepository->findAll();
        // Paginate the results of the query
        $appointments = $paginator->paginate(
            // Doctrine Query, not results
            $allAppointmentsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            2
        );
        return $this->render('gym/index.html.twig', [
            'gyms' => $appointments,
        ]);
    }

    /**
     * @Route("/row", name="gym_index_row", methods={"GET"})
     */
    public function indexrow(Request $request,GymRepository $gymRepository,PaginatorInterface $paginator): Response
    {
        $allAppointmentsQuery=$gymRepository->findAll();
        // Paginate the results of the query
        $appointments = $paginator->paginate(
            // Doctrine Query, not results
            $allAppointmentsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            3
        );
        return $this->render('gym/gym_liste_row.html.twig', [
            'gyms' => $appointments,
        ]);
    }

    /**
     * @Route("/videochat", name="videochat", methods={"GET"})
     */
    public function videochat(GymRepository $gymRepository): Response
    {   
        return $this->render('gym/videochat.html.twig');
    }

    /**
     * @Route("/new", name="gym_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gym = new Gym();
        $form = $this->createForm(GymType::class, $gym);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gym);
            $entityManager->flush();

            return $this->redirectToRoute('gym_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gym/new.html.twig', [
            'gym' => $gym,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="gym_show", methods={"GET"})
     */
    public function show(Request $request,Gym $gym,$id,PaginatorInterface $paginator): Response
    {
        $video = $this->getDoctrine()->getRepository(Course::class)->findBy(array('gym'=>$id));
        $allAppointmentsQuery = $this->getDoctrine()->getRepository(Subscription::class)->findBy(array('gym'=>$id));
        // Paginate the results of the query
        $appointments = $paginator->paginate(
            // Doctrine Query, not results
            $allAppointmentsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );


        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $iduser=$this->getUser()->getId();           
        }else{
            $iduser=0;
        }
        
            return $this->render('gym/show.html.twig', [
            'gym' => $gym,'videos'=>$video,'subs'=>$appointments ,'iduser'=>$iduser
        ]);
    }

    /**
     * @Route("/{id}/edit", name="gym_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Gym $gym, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GymType::class, $gym);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('gym_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gym/edit.html.twig', [
            'gym' => $gym,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="gym_delete", methods={"POST"})
     */
    public function delete(Request $request, Gym $gym, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gym->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gym);
            $entityManager->flush();
        }

        return $this->redirectToRoute('gym_index', [], Response::HTTP_SEE_OTHER);
    }
}

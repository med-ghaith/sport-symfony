<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Entity\User;
use App\Form\PlanningType;
use App\Repository\PlanningRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planning")
 */
class PlanningController extends AbstractController
{
    /**
     * @Route("/", name="planning_index", methods={"GET"})
     */
    public function index(PlanningRepository $planningRepository): Response
    {
        return $this->render('planning/index.html.twig', [
            'plannings' => $planningRepository->findAll(),
        ]);
    }
    /**
     * @Route("/indexFront", name="planning_index_front", methods={"GET"})
     */
    public function indexFront(PlanningRepository $planningRepository): Response
    {
        return $this->render('planning/ListPlanningFront.html.twig', [
            'plannings' => $planningRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="planning_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $planning = new Planning();
        $user = $this->getDoctrine()->getRepository(User::class)->find(30);

        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);
        $planning->setUser($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($planning);
            $entityManager->flush();

            return $this->redirectToRoute('planning_index', [], Response::HTTP_SEE_OTHER);
        }



        return $this->render('planning/new.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/newFront", name="planning_new_front", methods={"GET", "POST"})
     */
    public function newFront(Request $request, EntityManagerInterface $entityManager): Response
    {
        $planning = new Planning();
        $user=$this->getUser();
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);
        $planning->setUser($user);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($planning);
            $entityManager->flush();

            return $this->redirectToRoute('planning_index_front', [], Response::HTTP_SEE_OTHER);
        }



        return $this->render('planning/newFront.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="planning_show", methods={"GET"})
     */
    public function show(Planning $planning): Response
    {
        return $this->render('planning/show.html.twig', [
            'planning' => $planning,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="planning_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Planning $planning, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('planning_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planning/edit.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/editFront", name="planning_edit_front", methods={"GET", "POST"})
     */
    public function editFront(Request $request, Planning $planning, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlanningType::class, $planning);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('planning_index_front', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planning/edit_front.html.twig', [
            'planning' => $planning,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}", name="planning_delete", methods={"POST"})
     */
    public function delete(Request $request, Planning $planning, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$planning->getId(), $request->request->get('_token'))) {
            $entityManager->remove($planning);
            $entityManager->flush();
        }

        return $this->redirectToRoute('planning_index', [], Response::HTTP_SEE_OTHER);
    }
}

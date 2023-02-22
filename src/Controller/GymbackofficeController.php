<?php

namespace App\Controller;

use App\Entity\Gym;
use App\Form\GymType;
use App\Repository\GymRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard")
 */
class GymbackofficeController extends AbstractController
{
    /**
     * @Route("/gymbackoffice", name="app_gymbackoffice")
     */
    public function index(GymRepository $gymRepository): Response
    {
        return $this->render('back-office/gymbackoffice/index.html.twig', [
            'gyms' => $gymRepository->findAll(),
        ]);
    }
      /**
     * @Route("/gymdash/new", name="gymdash_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gym = new Gym();
        $form = $this->createForm(GymType::class, $gym);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gym);
            $entityManager->flush();

            return $this->redirectToRoute('app_gymbackoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back-office/gymbackoffice/new.html.twig', [
            'gym' => $gym,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gymdash/{id}", name="gymdash_show", methods={"GET"})
     */
    public function show(Gym $gym): Response
    {
        return $this->render('back-office/gymbackoffice/show.html.twig', [
            'gym' => $gym,
        ]);
    }

    /**
     * @Route("/{id}/gymdash/edit", name="gymdash_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Gym $gym, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GymType::class, $gym);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gymbackoffice', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('back-office/gymbackoffice/edit.html.twig', [
            'gym' => $gym,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/gymdash/delete/{id}", name="gymdash_delete", methods={"POST"})
     */
    public function delete(Request $request, Gym $gym, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gym->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gym);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gymbackoffice', [], Response::HTTP_SEE_OTHER);
    }
}

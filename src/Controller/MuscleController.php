<?php

namespace App\Controller;

use App\Repository\MuscleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/muscle")
 */
class MuscleController extends AbstractController
{
    /**
     * @Route("/", name="app_muscle")
     */
    public function index(): Response
    {
        return $this->render('muscle/index.html.twig', [
            'controller_name' => 'MuscleController',
        ]);
    }

    /**
     * @Route("/all", name="muslce_json", methods={"GET"})
     */
    public function getJsonEquipment(MuscleRepository $muscleRepository): Response
    {
        return $this->json($muscleRepository->findAll(),200,[],['groups'=>'exercise:read']);
    }

}

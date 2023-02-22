<?php

namespace App\Controller;

use App\Repository\ExerciceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackOfficeExerciceController extends AbstractController
{
    /**
     * @Route("/back/exercice", name="back_office_exercice")
     */
    public function index(ExerciceRepository $exerciceRepository): Response
    {
        return $this->render('back-office/back_office_exercice/index.html.twig', [
            'controller_name' => 'BackOfficeExerciceController',
            'exercices' => $exerciceRepository->findAll(),
        ]);
    }
}

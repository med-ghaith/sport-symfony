<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackOfficeDashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="back_office_dashboard")
     */
    public function index(): Response
    {
        return $this->render('back-office/back_office_dashboard/index.html.twig', [
            'controller_name' => 'BackOfficeDashboardController',
        ]);
    }
}

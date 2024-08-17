<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SalesController extends AbstractController
{
    #[Route('/sales', name: 'app_sales')]
    public function index(): Response
    {
        return $this->render('sales/index.html.twig', [
            'controller_name' => 'SalesController',
        ]);
    }
}

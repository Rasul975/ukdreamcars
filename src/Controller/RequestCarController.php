<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RequestCarController extends AbstractController
{
    #[Route('/request/car', name: 'app_request_car')]
    public function index(): Response
    {
        return $this->render('request_car/index.html.twig', [
            'controller_name' => 'Request A Car | UK Dream Cars',
        ]);
    }
}

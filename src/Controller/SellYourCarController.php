<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SellYourCarController extends AbstractController
{
    #[Route('/sell-your-car', name: 'app_sell_your_car')]
    public function index(): Response
    {
        return $this->render('sell_your_car/index.html.twig', [
            'controller_name' => 'Sell your Car | UK Dream Cars',
        ]);
    }
}

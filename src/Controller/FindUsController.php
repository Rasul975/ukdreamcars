<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FindUsController extends AbstractController
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }


    #[Route('/find/us', name: 'app_find_us')]
    public function index(): Response
    {
        return $this->render('find_us/index.html.twig', [
            'controller_name' => 'Find Us | UK Dream Cars',
            'google_maps_api_key' => $_ENV['GOOGLE_MAPS_API_KEY'],
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestimonialsController extends AbstractController
{
    #[Route('/testimonials', name: 'app_testimonials')]
    public function index(): Response
    {
        return $this->render('testimonials/index.html.twig', [
            'controller_name' => 'Testimonials | UK Dream Cars',
        ]);
    }
}

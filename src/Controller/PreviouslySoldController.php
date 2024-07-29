<?php

namespace App\Controller;

use App\Entity\Car;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class PreviouslySoldController extends AbstractController
{
    #[Route('/previously/sold', name: 'app_previously_sold')]
    public function index(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        // Fetch the query for sold cars
        $queryBuilder = $doctrine->getRepository(Car::class)->createQueryBuilder('c')
            ->where('c.isSold = true');

        // Paginate the results
        $pagination = $paginator->paginate(
            $queryBuilder, // Query to paginate
            $request->query->getInt('page', 1), // Current page number, 1 if not specified
            10 // Limit per page
        );

        return $this->render('previously_sold/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'Previously Sold Cars | UK Dream Cars',
        ]);
    }
}

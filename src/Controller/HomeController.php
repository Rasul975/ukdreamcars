<?php

namespace App\Controller;

use App\Entity\Car;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        // Get the QueryBuilder instance for the Car entity
        $carRepository = $entityManager->getRepository(Car::class);
        $queryBuilder = $carRepository->createQueryBuilder('c');

        // Build the query to fetch the last 3 records where isSold is false
        $queryBuilder->where('c.isSold = :isSold')
            ->setParameter('isSold', false)
            ->orderBy('c.id', 'DESC')  // Assuming id is auto-incremented
            ->setMaxResults(3);  // Limit to 3 results

        // Execute the query
        $carList = $queryBuilder->getQuery()->getResult();

        return $this->render('home/index.html.twig', [
            'cars' => $carList,
            'controller_name' => 'UK Dream Cars',
        ]);
    }
}

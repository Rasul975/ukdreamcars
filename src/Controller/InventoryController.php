<?php

namespace App\Controller;


use App\Entity\Car;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class InventoryController extends AbstractController
{
    #[Route('/inventory', name: 'app_inventory')]
    public function index(ManagerRegistry $doctrine, PaginatorInterface $paginator, Request $request): Response
    {
        $carRepository = $doctrine->getRepository(Car::class);

        // Get distinct makes from available cars
        $distinctMakes = $carRepository->createQueryBuilder('c')
            ->select('DISTINCT c.make')
            ->where('c.isSold = :isSold')
            ->setParameter('isSold', false)
            ->orderBy('c.make', 'ASC')
            ->getQuery()
            ->getResult();

        // Initialize the distinct models array
        $distinctModels = [];
        $selectedMake = $request->query->get('make', '');

        if ($selectedMake) {
            // Get distinct models from available cars for the selected make
            $distinctModels = $carRepository->createQueryBuilder('c')
                ->select('DISTINCT c.model')
                ->where('c.make = :make')
                ->andWhere('c.isSold = :isSold')
                ->setParameter('make', $selectedMake)
                ->setParameter('isSold', false)
                ->orderBy('c.model', 'ASC')
                ->getQuery()
                ->getResult();
        }

        // Build the query for available cars based on filters
        $queryBuilder = $carRepository->createQueryBuilder('c')
            ->where('c.isSold = :isSold')
            ->setParameter('isSold', false);

        // Apply search filters
        $make = $request->query->get('make', '');
        $model = $request->query->get('model', '');
        $price = $request->query->get('price', '');
        $sortField = $request->query->get('sortField', 'DateAdded');
        $sortDirection = $request->query->get('sortDirection', 'DESC');

        if (!empty($make)) {
            $queryBuilder->andWhere('c.make = :make')
                ->setParameter('make', $make);
        }
        if (!empty($model)) {
            $queryBuilder->andWhere('c.model = :model')
                ->setParameter('model', $model);
        }
        if (!empty($price)) {
            $queryBuilder->andWhere('c.price <= :price')
                ->setParameter('price', (int)$price);
        }

        // Apply sorting
        if (!empty($sortField)) {
            $queryBuilder->orderBy("c.$sortField", $sortDirection);
        }

        // Paginate the results
        $pagination = $paginator->paginate(
            $queryBuilder, // query, not result
            $request->query->getInt('page', 1), // page number
            10 // limit per page
        );

        return $this->render('inventory/index.html.twig', [
            'pagination' => $pagination,
            'distinctMakes' => $distinctMakes,
            'distinctModels' => $distinctModels,
            'selectedMake' => $selectedMake,
            'controller_name' => 'Inventory | UK Dream Cars',
            'sortField' => $sortField,
            'sortDirection' => $sortDirection,
        ]);
    }

    #[Route('/inventory/car/{id}', name: 'app_inventory_car', requirements: ['id' => '\d+'])]
    public function inventory(ManagerRegistry $doctrine, int $id): Response
    {
        $car = $doctrine->getRepository(Car::class)->find($id);

        if (!$car) {
            return $this->redirectToRoute('app_inventory');
        }


        return $this->render('inventory/car_details.html.twig', [
            'car' => $car,
            'controller_name' => 'Car Details | UK Dream Cars',
        ]);
    }
}

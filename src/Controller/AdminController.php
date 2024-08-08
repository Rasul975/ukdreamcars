<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\CarImage;
use App\Form\CarFormType;
use App\Form\FeaturesFormType;
use App\Form\ImageType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Client;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        // Get the 'status' parameter from the request
        $status = $request->query->get('status', 'all');

        // Start building the query
        $queryBuilder = $doctrine->getRepository(Car::class)->createQueryBuilder('c');

        // Apply filtering based on the status
        if ($status === 'sold') {
            $queryBuilder->where('c.isSold = :isSold')
                ->setParameter('isSold', true);
        } elseif ($status === 'available') {
            $queryBuilder->where('c.isSold = :isSold')
                ->setParameter('isSold', false);
        }

        // Paginate the results of the query
        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(), // query, not result
            $request->query->getInt('page', 1), // page number
            10 // limit per page
        );

        // Render the template with the car list and the current status
        return $this->render('admin/dashboard.html.twig', [
            'pagination' => $pagination,
            'status' => $status,
            'controller_name' => 'Admin | UK Dream Cars',
        ]);
    }


    #[Route('/admin/car/{id}', name: 'app_admin_car', requirements: ['id' => '\d+'])]
    public function car(ManagerRegistry $doctrine, int $id, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $car = $doctrine->getRepository(Car::class)->find($id);

        if (!$car) {
            throw $this->createNotFoundException('The car does not exist');
        }

        $image = new CarImage();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        // New features form
        $featuresForm = $this->createForm(FeaturesFormType::class, $car);
        $featuresForm->handleRequest($request);

        if ($featuresForm->isSubmitted() && $featuresForm->isValid()) {
            $newFeature = $featuresForm->get('feature')->getData();

            // Add the new feature to the car's features array
            if ($newFeature) {
                $car->addFeature($newFeature);
            }
            $entityManager->persist($car);
            $entityManager->flush();

            // Redirect to avoid form resubmission
            return $this->redirectToRoute('app_admin_car', ['id' => $car->getId()]);
        }

        // Handle image form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file upload exception
                    return $this->redirectToRoute('app_admin_car', ['id' => $car->getId()]);
                }
                $image->setPath($newFilename);
                $image->setCar($car);
                $entityManager->persist($image);
                $entityManager->flush();

                return $this->redirectToRoute('app_admin_car', ['id' => $car->getId()]);
            }
        }

        return $this->render('admin/car.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
            'featuresForm' => $featuresForm->createView(),
            'controller_name' => 'Car Details | UK Dream Cars',
        ]);
    }

    #[Route('/admin/car/new', name: 'app_admin_car_new')]
    public function newCar(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicle = new Car();
        $form = $this->createForm(CarFormType::class, $vehicle);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plateNumber = $form->get('registration')->getData();

            $existingVehicle = $entityManager->getRepository(Car::class)->findOneBy(['registration' => $plateNumber]);

            if ($existingVehicle) {
                $this->addFlash('error', 'Vehicle already added');
                return $this->redirectToRoute('app_admin');
            }

            $client = new Client();
            $apiUrl = 'https://driver-vehicle-licensing.api.gov.uk/vehicle-enquiry/v1/vehicles';
            $apiKey = $_ENV['API_KEY']; // Replace with your actual API key
            $headers = ['x-api-key' => $apiKey];
            $body = json_encode(['registrationNumber' => $plateNumber]);

            try {
                $response = $client->post($apiUrl, [
                    'headers' => $headers,
                    'body' => $body,
                    'verify' => false
                ]);

                // Extract the information you need from the JSON response
                $responseData = json_decode($response->getBody()->getContents(), true);
                // Check and format the response data
                $make = isset($responseData['make']) ? ucwords(strtolower($responseData['make'])) : 'Unknown';
                $colour = isset($responseData['colour']) ? ucwords(strtolower($responseData['colour'])) : 'Unknown';
                $fuel = isset($responseData['fuelType']) ? ucwords(strtolower($responseData['fuelType'])) : 'Unknown';
                $emissionClass = isset($responseData['euroStatus']) ? ucwords(strtolower($responseData['euroStatus'])) : 'Unknown';
                $engineSize = $responseData['engineCapacity'] ?? 0;
                $dateAdded = new DateTime();

                $vehicle->setMake($make);
                $vehicle->setYear($responseData['yearOfManufacture']);
                $vehicle->setColour($colour);
                $vehicle->setFuel($fuel);
                $vehicle->setEmissionClass($emissionClass);
                $vehicle->setEngineSize($engineSize);

                $vehicle->setDateAdded($dateAdded);

                $entityManager->persist($vehicle);
                $entityManager->flush();

            } catch (ClientException $e) {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                $errorMessage = $responseBody['errors'][0]['detail'];
                $this->addFlash('error', $errorMessage);
            }

            return $this->redirectToRoute('app_admin_car', ['id' => $vehicle->getId()]);
        }

        return $this->render('admin/new_car.html.twig', [
            'controller_name' => 'New Car | | UK Dream Cars',
            'carForm' => $form->createView(),
        ]);
    }


    #[Route('/admin/car/upload-image/success', name: 'app_admin_image-success')]
    public function success(): Response
    {
        return $this->render('admin/image_success.html.twig');
    }

    #[Route('/admin/car/{id}/sold', name: 'app_admin_car_sold')]
    public function markAsSold(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $car = $em->getRepository(Car::class)->find($id);

        if($car){
            $car->setIsSold(true);
            $em->persist($car);
            $em->flush();
        }
        return $this->redirectToRoute('app_admin_car', ['id' => $id]);
    }

    #[Route('/admin/car/{id}/available', name: 'app_admin_car_available')]
    public function markAsAvailable(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $car = $em->getRepository(Car::class)->find($id);

        if($car){
            $car->setIsSold(false);
            $em->persist($car);
            $em->flush();
        }
        return $this->redirectToRoute('app_admin_car', ['id' => $id]);
    }





    #[Route('/admin/car/{carId}/image/{id}', name: 'app_admin_delete_image')]
    public function deleteImage(ManagerRegistry $doctrine, int $carId, int $id, Filesystem $filesystem): Response
    {
        $entityManager = $doctrine->getManager();
        $image = $doctrine->getRepository(CarImage::class)->findOneBy(['id' => $id]);

        if (!$image) {
            throw $this->createNotFoundException('Image not found');
        }

        // Get the path to the image file
        $imagePath = $this->getParameter('images_directory') . '/' . $image->getPath();

        // Remove the image from the filesystem
        if ($filesystem->exists($imagePath)) {
            $filesystem->remove($imagePath);
        }

        // Remove the entity from the database
        $entityManager->remove($image);
        $entityManager->flush();

        $this->addFlash('success', 'Image deleted successfully.');

        // Redirect to the car details page
        return $this->redirectToRoute('app_admin_car', ['id' => $carId]);
    }
}

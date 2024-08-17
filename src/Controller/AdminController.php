<?php

namespace App\Controller;

use App\Entity\Car;
use App\Entity\CarImage;
use App\Entity\User;
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
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/admin', name: 'app_admin')]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $userCount = $doctrine->getRepository(User::class)->count();

        $carCount = $doctrine->getRepository(Car::class)->count(['isSold' => false]);

        $currentUser = $this->security->getUser();

        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'Admin | UK Dream Cars',
            'userCount' => $userCount,
            'carCount' => $carCount,
            'currentUser' => $currentUser,
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

                $vehicle->setRegistration($responseData['registrationNumber']);
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


    #[Route('/admin/car/{id}/edit', name: 'app_admin_car_edit')]
    public function editCar(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $car = $em->getRepository(Car::class)->find($id);

        // Check if the car exists
        if (!$car) {
            throw $this->createNotFoundException('The car does not exist');
        }

        // Create the form
        $form = $this->createForm(CarFormType::class, $car);

        // Handle the form submission
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the car to the database
            $em->persist($car);
            $em->flush();

            // Redirect to a success page or back to the car list
            return $this->redirectToRoute('app_admin');
        }

        // Render the form in the template
        return $this->render('admin/edit_car.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/inventory', name: 'app_admin_inventory')]
    public function inventory(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): Response
    {
        $status = $request->query->get('status', 'all');
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
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1), // page number
            10 // limit per page
        );

        return $this->render('admin/inventory.html.twig', [
            'pagination' => $pagination,
            'status' => $status,
            'controller_name' => 'Admin | UK Dream Cars',
        ]);
    }

    #[Route('/admin/car/{id}/delete', name: 'app_admin_car_delete')]
    public function deleteCar(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $car = $em->getRepository(Car::class)->find($id);

        // Check if the car exists
        if (!$car) {
            throw $this->createNotFoundException('The car does not exist');
        }

        // Remove the car from the database
        $em->remove($car);
        $em->flush();

        return $this->redirectToRoute('app_admin_inventory');
    }


    #[Route('/admin/users', name: 'app_admin_users')]
    public function manageUsers(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $userRepository = $em->getRepository(User::class);

        // Retrieve all users
        $allUsers = $userRepository->findAll();

        // Get the currently logged-in user
        $currentUser = $this->security->getUser();

        // Filter out the currently logged-in user
        $users = array_filter($allUsers, function($user) use ($currentUser) {
            return $user !== $currentUser;
        });

        return $this->render('admin/manage_users.html.twig', [
            'controller_name' => 'Admin, Manage Users | UK Dream Cars',
            'users' => $users,
        ]);
    }
    #[Route('/admin/users/{id}/delete', name: 'app_admin_users_delete')]
    public function deleteUser(ManagerRegistry $doctrine, $id): Response
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        // Check if the user is an admin
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            // Count how many users have the admin role
            $adminCount = $em->getRepository(User::class)->count(['roles' => 'ROLE_ADMIN']);

            if ($adminCount <= 1) {
                // Prevent deletion and return an error message
                // $this->addFlash('error', 'You cannot delete the last admin user.');
                return $this->redirectToRoute('app_admin_users');
            }
        }

        // Remove the user from the database
        $em->remove($user);
        $em->flush();

        // $this->addFlash('success', 'User deleted successfully.');
        return $this->redirectToRoute('app_admin_users');
    }

    #[Route('/admin/users/{id}/roles/{role}', name: 'app_admin_users_roles')]
    public function changeUserRole(ManagerRegistry $doctrine, $id, $role): Response
    {
        $em = $doctrine->getManager();
        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        $newRoles = [];

        if ($role === 'ROLE_ADMIN') {
            $newRoles = ['ROLE_ADMIN', 'ROLE_SALES']; // Admin gets both Admin and Sales roles
        } elseif ($role === 'ROLE_SALES') {
            $newRoles = ['ROLE_SALES', 'ROLE_USER']; // Sales gets Sales and User roles
        } else {
            $newRoles = ['ROLE_USER']; // Default to User role for any other case
        }

        // Update the user roles
        $user->setRoles($newRoles);
        $em->flush();

        // $this->addFlash('success', 'User role updated successfully.');
        return $this->redirectToRoute('app_admin_users');
    }
}

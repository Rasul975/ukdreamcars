<?php

namespace App\Controller;

use App\Form\PartExchangeFormType;
use Mailtrap\Config;
use Mailtrap\MailtrapClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Mailtrap\EmailHeader\CategoryHeader;

class PartExchangeController extends AbstractController
{
    #[Route('/part-exchange', name: 'app_part_exchange')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(PartExchangeFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $apiKey =  $_ENV['API_KEY_MAIL'];
            $mailtrap = new MailtrapClient(new Config($apiKey));

            $email = (new Email())
                ->from(new Address('mailtrap@demomailtrap.com', 'Mailtrap Test'))
                ->to(new Address("ukdreamcars0@gmail.com"))
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Part Exchange Request')
                ->text('Name: ' . $data['name'] . "\n" .
                    'Email: ' . $data['email'] . "\n" .
                    'Phone: ' . $data['phone'] . "\n" .
                    'Interested In: ' . $data['interestedIn'] . "\n" .
                    'Message: ' . $data['message'] . "\n" .
                    'Make & Model: ' . $data['makeModel'] . "\n" .
                    'Registration: ' . $data['registration'] . "\n" .
                    'Mileage: ' . $data['mileage'] . "\n" .
                    'Transmission: ' . $data['transmission'] . "\n" .
                    'Fuel Type: ' . $data['fuelType'] . "\n" .
                    'Exterior Colour: ' . $data['exteriorColour'] . "\n" .
                    'Interior Colour: ' . $data['interiorColour'] . "\n" .
                    'Interior Finish: ' . $data['interiorFinish'] . "\n" .
                    'Full Service History: ' . $data['fullServiceHistory'] . "\n" .
                    'Last Serviced: ' . $data['lastServiced'] . "\n" .
                    'Previous Owners: ' . $data['previousOwners'] . "\n" .
                    'Condition: ' . $data['condition'])
                ->html(
                    '<html>
                <body>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr style="background-color: #f2f2f2;">
                            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Field</th>
                            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Details</th>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Name</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Email</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Phone</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Interested In</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['interestedIn'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Message</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . nl2br(htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8')) . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Make & Model</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['makeModel'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Registration</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['registration'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Mileage</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['mileage'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Transmission</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['transmission'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Fuel Type</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['fuelType'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Exterior Colour</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['exteriorColour'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Interior Colour</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['interiorColour'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Interior Finish</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['interiorFinish'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Full Service History</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['fullServiceHistory'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Last Serviced</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['lastServiced'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Previous Owners</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . htmlspecialchars($data['previousOwners'], ENT_QUOTES, 'UTF-8') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">Condition</td>
                            <td style="padding: 8px; border: 1px solid #ddd;">' . nl2br(htmlspecialchars($data['condition'], ENT_QUOTES, 'UTF-8')) . '</td>
                        </tr>
                    </table>
                </body>
                </html>'
                );

            $email->getHeaders()
                ->add(new CategoryHeader('Integration Test'))
            ;

            $response = $mailtrap->sending()->emails()->send($email);

            // Optionally, you can add a flash message to indicate success
            $this->addFlash('success', 'Your message has been sent!');

            return $this->redirectToRoute('app_part_exchange');
        }

        return $this->render('part_exchange/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

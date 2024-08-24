<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Mailtrap\Config;
use Mailtrap\MailtrapClient;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Mailtrap\EmailHeader\CategoryHeader;

class ContactUsController extends AbstractController
{

//    #[Route('/contact/us', name: 'app_contact_us')]
//    public function index(Request $request, MailerInterface $mailer): Response
//    {
//        $form = $this->createForm(ContactFormType::class);
//
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $data = $form->getData();
//
//            $email = (new Email())
//                ->from(new Address('mailtrap@ukdreamcars.co.uk', 'UK Dream Cars'))
//                ->to(new Address("info@ukdreamcars.co.uk"))
//                ->subject('Contact Us Form Submission')
//                ->text('Sender: ' . $data['name'] . "\nEmail: " . $data['email'] . "\nMessage: " . $data['message'])
//            ;
//
//            $email->getHeaders()
//                ->add(new CategoryHeader('Integration Test'))
//            ;
//
//            $mailer->send($email);
//
//            // Optionally, you can add a flash message to indicate success
//            $this->addFlash('success', 'Your message has been sent!');
//
//            return $this->redirectToRoute('app_contact_us');
//        }
//
//
//        return $this->render('contact_us/index.html.twig', [
//            'contactForm' => $form->createView(),
//            'controller_name' => 'Contact Us | UK Dream Cars',
//        ]);
//    }

    #[Route('/contact/us', name: 'app_contact_us')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $apiKey =  $_ENV['API_KEY_MAIL'];;
            $mailtrap = new MailtrapClient(new Config($apiKey));

            $email = (new Email())
                ->from(new Address('mailtrap@ukdreamcars.co.uk', 'UK Dream Cars'))
                ->to(new Address("info@ukdreamcars.co.uk"))
                ->subject('Contact Us Form Submission')
                ->text('Sender: ' . $data['name'] . "\nEmail: " . $data['email'] . "\nMessage: " . $data['message'])
            ;

            $email->getHeaders()
                ->add(new CategoryHeader('Integration Test'))
            ;

            $response = $mailtrap->sending()->emails()->send($email);

            // Optionally, you can add a flash message to indicate success
            $this->addFlash('success', 'Your message has been sent!');

            return $this->redirectToRoute('app_contact_us');
        }


        return $this->render('contact_us/index.html.twig', [
            'contactForm' => $form->createView(),
            'controller_name' => 'Contact Us | UK Dream Cars',
        ]);
    }
}

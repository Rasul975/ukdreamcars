<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ErrorController extends AbstractController
{
    #[Route('/error/{code}', name: 'app_error')]
    public function index(Request $request): Response
    {
        $code = $request->attributes->get('code');
        $exception = $request->attributes->get('exception');

        $statusText = $exception ? $exception->getMessage() : Response::$statusTexts[$code] ?? 'Unknown error';

        return $this->render('error/index.html.twig', [
            'code' => $code,
            'message' => $statusText,
        ]);
    }
}

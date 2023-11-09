<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/service/{name}', name: 'app_service')]
    public function showService($name): Response
    {
        return $this->render('service/showService.html.twig', [
            'name'=>$name,
        ]);
    }

    #[Route('/go', name: 'app_go')]
    public function goToIndex():Response
    {
        return $this->redirectToRoute("app_home"); //rediriger vers le nom de la route
        //redirect => rediriger vers l'URL
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            
        ]);
    }

    #[Route('/marque', name: 'app_marque')]
    public function marque(): Response
    {

        return $this->render('main/marque.html.twig', [
            'controller_name' => 'MarqueController',
        ]);
    }

}

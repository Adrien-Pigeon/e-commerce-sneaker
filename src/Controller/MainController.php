<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{


    #[Route('/', name: 'app_main_')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', []);
    }


    #[Route('/marque', name: 'marque')]
    public function marque(): Response
    {

        return $this->render('main/marque.html.twig', [
            'controller_name' => 'MarqueController',
        ]);
    }
    

    #[Route('/collections', name: 'collections')]
    public function collections(CategoriesRepository $categoriesRepository): Response // Le repository permet de récupérer en base de données
    {

        return $this->render('main/collections.html.twig', [
            'categories' => $categoriesRepository->findBy(
                [], //findby avec le [] permet de tous récupérer dans Catégories
                ['categoryOrder' => 'asc']
            ) // On récupère le numéro des catégories dans l'ordre croissant
        ]);
    }
}

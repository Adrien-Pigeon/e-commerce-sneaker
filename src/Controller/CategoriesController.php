<?php

namespace App\Controller;

use App\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'app_categories_')]
class CategoriesController extends AbstractController
{
 
    #[Route('/{slug}', name:'list')] // {slug} signifie que la valeur du slug est variable
    public function list(Categories $category): Response
    {
        // On va chercher la liste des produits de la catégorie
        $products = $category->getProducts();


        return $this->render('categories/list.html.twig', [
            'category' => $category,
            'products' => $products
        ]);

    }
}

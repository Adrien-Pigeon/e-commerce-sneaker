<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories', name: 'app_categories_')]
class CategoriesController extends AbstractController
{
 
    #[Route('/{slug}', name:'list')] // {slug} signifie que la valeur du slug est variable
    public function list(Categories $category, ProductsRepository $productsRepository, Request $request): Response
    {
        //On va chercher le numéro de page dans l'url
        $page = $request->query->getInt('page', 1);

        // On va chercher la liste des produits de la catégorie, le numéro défini le nombre de produit sur une page
        $products = $productsRepository->findProductsPaginated($page, $category->getSlug(), 4);

        return $this->render('categories/list.html.twig', [
            'category' => $category,
            'products' => $products
        ]);

    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Images;
use App\Entity\Products;
use App\Form\ProductsFormType;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/produits', name: 'app_admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig');
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un nouveau produit
        $product = new Products();

        //On crée le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        //On traite la requete de formulaire
        $productForm->handleRequest($request);

        //On vérifie si le formulaire est soumis et valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {

            //On récupère les images
            $images = $productForm->get('images')->getData();
            //dd($images);

            foreach ($images as $image) {
                //On définit le dossier de destination
                $folder = 'products';

                //On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }

            //On génère le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
            //dd($slug);

            //On arrondit le prix
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);
            //dd($prix);

            //On envoi en BDD
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');

            //On redirige en cas de succès
            return $this->redirectToRoute('app_admin_products_index');
        }

        return $this->render('admin/products/add.html.twig', [
            'productForm' => $productForm->createView()
        ]);
    }

    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Products $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, PictureService $pictureService): Response
    {
        //On vérifie si l'utilisateur peut editer avec le VOTER
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        //On divise le prix par 100
        $prix = $product->getPrice() / 100;
        $product->setPrice($prix);

        //On crée le formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        //On traite la requete de formulaire
        $productForm->handleRequest($request);

        //On vérifie si le formulaire est soumis et valide
        if ($productForm->isSubmitted() && $productForm->isValid()) {

            //On récupère les images
            $images = $productForm->get('images')->getData();
            //dd($images);

            foreach ($images as $image) {
                //On définit le dossier de destination
                $folder = 'products';

                //On appelle le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                $img = new Images();
                $img->setName($fichier);
                $product->addImage($img);
            }

            //On génère le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);
            //dd($slug);

            //On arrondit le prix
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);
            //dd($prix);

            //On envoi en BDD
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit modifié avec succès !');

            //On redirige en cas de succès
            return $this->redirectToRoute('app_admin_products_index');
        }

        return $this->render('admin/products/edit.html.twig', [
            'productForm' => $productForm->createView(),
            'product' => $product
        ]);
    }


    #[Route('/suppresion/{id}', name: 'delete')]
    public function delete(Products $product): Response
    {
        //On vérifie si l'utilisateur peut supprimer avec le VOTER
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        return $this->render('admin/products/index.html.twig');
    }


    #[Route('/suppression/image/{id}', name: 'delete_image', methods: ['DELETE'])]
    public function deleteImage(Images $image, Request $request, EntityManagerInterface $em, PictureService $pictureService): JsonResponse
    {
        //On récupère le contenu de la requete
        $data = json_decode( $request->getContent(), true);

        if($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])){

            //le token csrf est valide
            //On récupère le nom de l'image
            $nom = $image->getName();

            if($pictureService->delete($nom, 'products', 300, 300)){

                $em->remove($image);
                $em->flush();

                //La suppression à réussi
                return new JsonResponse(['success' => true], 200);
            }
            //La suppression a échoué
            return new JsonResponse(['error' => 'Erreur de suppression'], 400);
        }


       return new JsonResponse(['error' => 'Token invalide'], 400);
    }

}

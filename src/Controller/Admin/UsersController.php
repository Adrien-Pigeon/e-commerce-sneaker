<?php

namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/utilisateur', name: 'app_admin_users')]
class UsersController extends AbstractController
{
    #[Route('/' , name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/users/index.html.twig');
    }
}
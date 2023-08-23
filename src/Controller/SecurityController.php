<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UsersRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername, 
        'error' => $error]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/mdp-oubli', name:'forgotten_password')]
    public function forgottenPassword(Request $request, UsersRepository $usersRepository, 
    TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        // crée le formulaire
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        // traitement du formulaire
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // On va chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());

            //dd($user);

            // On vérifie si un utilisateur est présent
            if($user){

                // On génère un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);

                // on envoi en BDD le token
                $entityManager->persist($user);
                $entityManager>flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // On crée les données du mail
                // $context = [
                //     'url' => $url,
                //     'user' => $user
                // ];
                $context = compact('url','user');

                // On envoi le mail
                $mail->send(
                    'no-reply@sneakers-ecommerce.fr',
                    $user->getEmail(),
                    'Réinitialisation du mot de passe',
                    'password_reset',
                    $context
                );

                // Si l'utilisateur est bon, on redirige et le mail est envoyé
                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');

            }

            // On redirige si l'utilisateur est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        // Crée la vue
        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/mdp-oubli/{token}', name:'reset_pass')]
    public function resetPassword(string $token, Request $request, UsersRepository $usersRepository,
    EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // On vérifie si on a ce token dans la BDD
        $user = $usersRepository->findOneByResetToken($token);
        
        // enlever le "!" si on montre à l'exam
        if(!$user){

            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);
            
            if($form->isSubmitted() && $form->isValid()){

                //on efface le token
                $user->setResetToken('');
                // On hash le password
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                // On envoi en BDD
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }

        // si le token est null, on redirige
        $this->addFlash('danger', 'jeton invalide');
        return $this->redirectToRoute('app_login');

    }
}

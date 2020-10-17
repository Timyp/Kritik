<?php

namespace App\Controller;

use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        //1. création du formulaire
        $form = $this->createForm(UserRegistrationFormType::class);
        //2. traitement de la requête: modification des valeurs et validations
        $form->handleRequest($request);

        //3.vérifier si le formulaire est envoyé et valide
        if($form->isSubmitted() && $form->isValid()){
            //4. récupération des données
            /** @var User $user */
            $user = $form->getData();

            //hashage du mot de passe
//            $password = $form['plainPassword']->getData();
            $password = $form->get('plainPassword')->getData();
            $hash = $encoder->encodePassword($user, $password);
            $user->setPassword($hash);

            //Enregistrement en base
            $manager->persist($user);
            $manager->flush();

            //Message flash et redirection
            $this->addFlash('success', 'Vous avez été inscrit, vous pouvez vous connecter');

            return $this->redirectToRoute('app_login');
        }

        //Envois de la vue du formulaire
        return $this->render('security/register.html.twig', [
            'register_form' => $form->createView(),
        ]);
    }
}

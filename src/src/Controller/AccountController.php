<?php


namespace App\Controller;


use App\Form\UserRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 *@Route("/account", name="account_")
 *@IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        /** @var User $user */
        $user = $this->getUser();

        //Enregistrer l'email pour le redefinir en cas d'erreur du formulaire (éviter la deconnexion)
        $email = $user->getEmail();

        $form = $this->createForm(UserRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //Mettre à jour le mot de passe si nécessaire
            $password = $form->get('plainPassword')->getData();
            if($password !== null) {
                $hash = $encoder->encodePassword($user, $password);
                $user->setPassword($hash);
            }

            $manager->flush();
            $this->addFlash('sucees', 'Vos informations sont à jour');
        } else {
            $user->setEmail($email);
        }

        return $this->render('account/profile.html.twig', [
            'profile_form' => $form->createView(),
        ]);
    }
}
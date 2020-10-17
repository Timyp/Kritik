<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //Detecter si on modifie un utilisateur ou non
        $user = $options['data'] ?? null;
        $isEdit = $user instanceof User && $user->getId() !== null;
        //Si on modifie le mot de passe est facultatif
        $passwordConstraints = $isEdit ? [] : [new NotBlank(['message' => 'Mot de passe manquant.'])];

        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Adresse email manquante.']),
                    new Email(['message' => 'Adresse email invalide']),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'L\'adresse ne doit pas dépasser {{ limit }} caractères.'
                    ]),
                ]
            ])
            ->add('pseudo', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Pseudo manquant.']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'le pseudo doit contenir au moin {{ limit }} caractères.',
                        'max' => 50,
                        'maxMessage' => 'Le pseudo ne doit dépasser les {{ limit }} caractères.'
                    ]),
                    new Regex([
                        'pattern' => '~^[a-zA-Z0-9_-]+$~',
                        'message' =>  'Le pseudo doit contenir des caractères alphanumériques non accentués, tirets et underscores.'
                    ])
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'required' => !$isEdit,
                //le champ de formulaire n'est pas lié au User
                'mapped' => false,
                //message d'erreur si les mdp ne corresponde pas
                'invalid_message' => 'Les messasges ne correspondent pas.',
                //type de champs à repeter
                'type' => PasswordType::class,
                'constraints' => array_merge($passwordConstraints, [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ])
                ])
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

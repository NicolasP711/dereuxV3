<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;



class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('pseudonym', TextType::class, [
            'label' => 'Pseudonyme',
            'help' => 'Le pseudonyme doit contenir entre 2 et 50 caractères.',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un pseudonyme.',
                ]),
                new Length([
                    'min' => 2,
                    'minMessage' => 'Votre pseudonyme doit contenir au moins {{ limit }} caractères.',
                    'max' => 40,
                    'maxMessage' => 'Votre pseudonyme doit contenir au maximum {{ limit }} caractères.',
                ]),
            ],
            'attr' => [
                'id' => 'nom'
            ]
        ])
        ->add('email', TextType::class, [
            'label' => 'Adresse Email',
            'constraints' => [
                new Email([
                    'message' => 'L\'adresse email {{ value }} n\'est pas une adresse email valide.'
                ]),
                new NotBlank([
                    'message' => 'Merci de renseigner une adresse email.'
                ]),
            ]
        ])
        ->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Le mot de passe ne correspond pas à sa confirmation.',
            'first_options' => [
                'label' => 'Mot de passe',
                'help' => 'Le mot de passe doit contenir au minimum 8 caractères dont une minuscule, une majuscule, un chiffre et un caractère spécial.',
            ],
            'second_options' => [
                'label' => 'Confirmation du mot de passe.',
            ],
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez renseigner un mot de passe.',
                ]),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères.',
                    // max length allowed by Symfony for security reasons
                    'max' => 255,
                    'maxMessage' => 'Votre mot de passe doit contenir au maximum {{ limit }} caractères.'
                ]),
                new Regex([
                    'pattern' => "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{0,4096}$/",
                    'message' => 'Le mot de passe doit contenir obligatoirement une minuscule, une majuscule, un chiffre et un caractère spécial.',
                ])
            ]
        ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}

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
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator;
use Symfony\Component\Validator\Constraints\NotNull;

class EditUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('pseudonym', TextType::class, [
            'label' => 'Pseudonyme',
            'help' => 'Le pseudonyme doit contenir entre 2 et 50 caractères',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un pseudonyme',
                ]),
                new Length([
                    'min' => 2,
                    'minMessage' => 'Votre pseudonyme doit contenir au moins {{ limit }} caractères',
                    'max' => 50,
                    'maxMessage' => 'Votre pseudonyme doit contenir au maximum {{ limit }} caractères',
                ]),
            ],
            'attr' => [
                'id' => 'nom'
            ]
        ])
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'constraints' => [
                new Email([
                    'message' => 'L\'adresse email renseignée n\'est pas une adresse email valide'
                ]),
                new NotBlank([
                    'message' => 'Merci de renseigner une adresse email'
                ]),
            ]
        ])
        ->add('currentpassword', PasswordType::class, [
            'label'=>'Mot de passe actuel',
            'mapped' => false,
            'required' => true,
            'constraints' => [
                new UserPassword([
                    'message' => 'Le mot de passe doit correspondre au mot de passe actuel'
                ]),
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

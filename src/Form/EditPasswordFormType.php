<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class EditPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('currentpassword', PasswordType::class, array('label'=>'Mot de passe actuel',
        'mapped' => false,
        'required' => true,
        'constraints' => [
            new NotBlank([
                'message' => 'Merci de renseigner votre mot de passe actuel'
            ]),
            new UserPassword([
                'message' => 'Le mot de passe doit correspondre au mot de passe actuel'
            ]),
        ]
    ))

    ->add('plainPassword', RepeatedType::class, [
        'type' => PasswordType::class,
        'invalid_message' => 'Le mot de passe ne correspond pas à sa confirmation',
        'first_name' => 'password',
        'second_name' => 'confirm',
        'first_options' => [
            'label' => 'Nouveau mot de passe',
            'help' => 'Le nouveau mot de passe doit contenir au minimum 8 caractères dont une minuscule, une majuscule, un chiffre et un caractère spécial',
        ],
        'second_options' => [
            'label' => 'Confirmation du nouveau mot de passe',
        ],
        'mapped' => false,
        'constraints' => [
            new NotBlank([
                'message' => 'Veuillez renseigner un nouveau mot de passe',
            ]),
            new Length([
                'min' => 8,
                'minMessage' => 'Votre nouveau mot de passe doit contenir au moins {{ limit }} caractères',
                // max length allowed by Symfony for security reasons
                'max' => 255,
                'maxMessage' => 'Votre nouveau mot de passe doit contenir au maximum {{ limit }} caractères'
            ]),
            new Regex([
                'pattern' => "/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{0,255}$/",
                'message' => 'Le nouveau mot de passe doit contenir obligatoirement une minuscule, une majuscule, un chiffre et un caractère spécial',
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

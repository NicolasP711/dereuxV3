<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditPhotoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('photo', FileType::class, [
            'label' => 'Selectionnez une nouvelle photo',
            'attr' => [
                'accept' => 'image/jpeg, image/png',
                'class' => 'mb-4',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Vous devez sélectionner un fichier'
                ]),
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'L\'image doit être de type jpg ou png',
                    'maxSizeMessage' => 'Fichier trop volumineux {{ size }}{{ suffix }}. La taille maximum autorisée est {{ limit }}{{ suffix }}',
                ])
            ],
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Modifier ma photo de profil',
            'attr' => [
                'class' => 'btn defaultBtn w-100',
            ],
        ])
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

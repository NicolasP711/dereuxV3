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
use Symfony\Component\Validator\Constraints\Image;

class EditPhotoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('photo', FileType::class, [
            'label' => 'Selectionnez une nouvelle image',
            'help' => 'Largeur: 1024 à 4096 pixels. Hauteur: 768 à 2160 pixels',
            'attr' => [
                'accept' => 'image/jpeg, image/png',
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
                ]),
                new Image([
                    'minWidth' => '1024',
                    'minHeight' => '768',
                    'maxWidth' => '4096',
                    'maxHeight' => '2160',
                    'minWidthMessage' => 'Votre fichier fait {{ width }} pixels de large. La largeur minimum est de 1024px (la hauteur minimum est de 768 pixels)',
                    'minHeightMessage' => 'Votre fichier fait {{ height }} pixels de haut. La hauteur minimum est de 768px (la largeur minimum est de 1024 pixels)',
                    'maxWidthMessage' => 'Votre fichier fait {{ width }} pixels de large. La largeur maximum est de 4096px (la hauteur maximum est de 2160 pixels)',
                    'maxHeightMessage' => 'Votre fichier fait {{ height }} pixels de haut. La hauteur maximum est de 2160px (la largeur maximum est de 4096 pixels)',


                ])
            ],
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Modifier',
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
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Artwork;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArtworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Titre',
            'help' => 'Le titre doit contenir entre 3 et 150 caractères',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un titre'
                ]),
                new Length ([
                    'min' => 3,
                    'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                    'max' => 150,
                    'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères',
                ]),
            ]
        ])
        ->add('description', CKEditorType::class, [
            'label' => 'Description',
            'purify_html' => true,
            'help' => 'La description doit contenir au maximum 3000 caractères',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un contenu'
                ]),
                new Length ([
                    'max' => 3000,
                    'maxMessage' => 'Le contenu doit contenir au maximum {{ limit }} caractères',
                ]),
            ],
            'config' => array(
                'extraPlugins' => 'wordcount',
            ),
            'plugins' => array(
                'wordcount' => array(
                    'path'     => '/bundles/fosckeditor/plugins/wordcount/', // with trailing slash
                    'filename' => 'plugin.js',
                ),
            ),
        ])
        ->add('picture', FileType::class, [
            'label' => 'Selectionnez une image',
            'data_class' => null,
            'attr' => [
                'accept' => 'image/jpeg, image/png',
                'class' => 'mb-4',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Vous devez sélectionner une image'
                ]),
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'L\'image doit être de type jpeg ou png',
                    'maxSizeMessage' => 'Fichier trop volumineux {{ size }}{{ suffix }}. La taille maximum autorisée est {{ limit }}{{ suffix }}',
                ])
            ],
        ])
        ->add('artist', TextType::class, [
            'label' => 'Artiste',
            'help' => 'Le nom de l\'artiste doit contenir entre 3 et 150 caractères',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un artiste'
                ]),
                new Length ([
                    'min' => 3,
                    'minMessage' => 'L\'artiste doit contenir au moins {{ limit }} caractères',
                    'max' => 150,
                    'maxMessage' => 'L\'artiste doit contenir au maximum {{ limit }} caractères',
                ]),
            ]
        ])
            ->add('creationDate', DateType::class, [
                'label' => 'Date de création de l\'oeuvre',
                'widget' => 'choice',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner une date de création'
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Artwork::class,
        ]);
    }
}

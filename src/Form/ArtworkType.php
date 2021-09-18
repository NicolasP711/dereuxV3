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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Image;

class ArtworkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('picture', FileType::class, [
            'label' => 'Selectionnez une image',
            'help' => 'Largeur: 1024 à 4096 pixels. Hauteur: 768 à 2160 pixels',
            'data_class' => null,
            'attr' => [
                'accept' => 'image/jpeg, image/png',
                'class' => 'mb-4',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner une image'
                ]),
                new File([
                    'maxSize' => '10M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'L\'image doit être de type jpeg ou png',
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
            'help' => 'La description doit contenir au maximum 3000 caractères',
            'purify_html' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner une description'
                ]),
                new Length ([
                    'max' => 3000,
                    'maxMessage' => 'La description doit contenir au maximum {{ limit }} caractères',
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
            ->add('yearOfCreation', ChoiceType::class, [
                'label' => 'Année de création de l\'oeuvre',
                'required' => false,
                'help' => 'Facultatif, si vous laissez ce champ vide, l\'année de création sera considérée comme inconnue',
                'placeholder' => 'Choisissez une année de création',
                'choices' => $this->getYears(0),
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'btn defaultBtn mt-3 col-12',
                ],
            ])
        ;
    }

    private function getYears($min, $max='current')
    {
         $years = range($min, ($max === 'current' ? date('Y') : $max));

         return array_combine($years, $years);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Artwork::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}

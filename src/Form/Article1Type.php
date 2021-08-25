<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class Article1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Titre',
            'help' => 'Le titre doit contenir entre 3 et 100 caractères',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un titre'
                ]),
                new Length ([
                    'min' => 3,
                    'minMessage' => 'Le titre doit contenir au moins {{ limit }} caractères',
                    'max' => 100,
                    'maxMessage' => 'Le titre doit contenir au maximum {{ limit }} caractères',
                ]),
            ]
        ])
        ->add('content', CKEditorType::class, [
            'label' => 'Contenu',
            'purify_html' => true,
            'help' => 'Le contenu doit contenir au maximum 10 000 caractères',
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un contenu'
                ]),
                new Length ([
                    'max' => 10000,
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
        ->add('save', SubmitType::class, [
            'label' => 'Publier',
            'attr' => [
                'class' => 'btn btn-success col-12',
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}

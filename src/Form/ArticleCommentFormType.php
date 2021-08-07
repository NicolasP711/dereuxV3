<?php

namespace App\Form;

use App\Entity\ArticleComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleCommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('content', TextareaType::class, [
            'label' => 'Contenu',
            'help' => 'Le contenu doit contenir entre 10 et 2000 caractères.',
            'purify_html' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un contenu.'
                ]),
                new Length ([
                    'min' => 10,
                    'minMessage' => 'Le contenu doit contenir au moins 10 caractères.',
                    'max' => 2000,
                    'maxMessage' => 'Le contenu doit contenir au maximum {{ limit }} caractères.',
                ]),
            ]
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Publier',
            'attr' => [
                'class' => 'btn btn-success col-12 my-3',
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArticleComment::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}

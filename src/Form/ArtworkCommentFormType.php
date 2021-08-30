<?php

namespace App\Form;

use App\Entity\ArtworkComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArtworkCommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('content', TextareaType::class, [
            'label' => 'Commentaire',
            'help' => 'Le commentaire doit contenir entre 10 et 2000 caractères.',
            'purify_html' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de renseigner un commentaire.'
                ]),
                new Length ([
                    'min' => 10,
                    'minMessage' => 'Le commentaire doit contenir au moins 10 caractères.',
                    'max' => 2000,
                    'maxMessage' => 'Le commentaire doit contenir au maximum {{ limit }} caractères.',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ArtworkComment::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}

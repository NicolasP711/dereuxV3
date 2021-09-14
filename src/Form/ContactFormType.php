<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Validator\Constraints\Email;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom ou pseudonyme',
                'help' => 'Le nom ou pseudonyme doit contenir entre 2 et 120 caractères',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner votre nom ou un pseudonyme',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Votre nom complet ou votre pseudonyme doit contenir au moins {{ limit }} caractères',
                        'max' => 120,
                        'maxMessage' => 'Votre nom complet ou votre pseudonyme doit contenir au maximum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'constraints' => [
                    new Email([
                        'message' => 'L\'adresse email renseignée n\'est pas une adresse email valide'
                    ]),
                    new NotBlank([
                        'message' => 'Merci de renseigner une adresse email'
                    ]),
                ]
            ])
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'help' => 'Le sujet doit contenir entre 2 et 100 caractères',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un sujet',
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le sujet doit contenir au moins {{ limit }} caractères',
                        'max' => 100,
                        'maxMessage' => 'Le sujet doit contenir au maximum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'help' => 'Le message doit contenir entre 5 et 2000 caractères',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de renseigner un message',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Le message doit contenir au moins {{ limit }} caractères',
                        'max' => 2000,
                        'maxMessage' => 'Le message doit contenir au maximum {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn mt-3 defaultBtn col-12',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}

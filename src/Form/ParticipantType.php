<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextareaType::class, [
                'label' => "Pseudo"
            ])
            ->add('prenom', TextareaType::class, [
                'label' => "Prénom"
            ])
            ->add('nom', TextareaType::class, [
                'label' => "Nom"
            ])
            ->add('telephone')
            ->add('mail', TextareaType::class, [
                'label' => "Adresse mail"
            ])
            ->add('password',PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                ],
            ])
            ->add('confirmation',PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                ],
            ])
            ->add('Photo', FileType::class,
                [
                    'mapped'=>false,
                    'constraints'=>[
                        new Image([
                            'maxSize'=>'7024k',
                            'mimeTypesMessage'=>'Image format not allowed !',

                        ])
                    ],
                    'required' => false
                ])
            ->add('rattachement', EntityType::class, [
                // looks for choices from this entity
                'class' => Site::class,
                // uses the name
                'choice_label' => 'nomSite',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}

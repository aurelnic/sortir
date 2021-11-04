<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', null, [
                'label' => "Libelle de la sortie"
            ])
            ->add('dateSortie', DateTimeType::class, [
                'label' => "Date de la sortie"
            ])
            ->add('dateCloture', DateType::class, [
                'label' => "Date limite d'inscription"
            ])
            ->add('nbreParticipants', IntegerType::class, [
                'label' => "Nombre de participants"
            ])
            ->add('duree', IntegerType::class, [
                'label' => "Durée de la sortie"
            ])
            ->add('description', TextareaType::class, [
                'label' =>"Description de la sortie"
            ])

            ->add('lieu',EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=>function($lieu){
                    return $lieu->getNomLieu();
                }])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}

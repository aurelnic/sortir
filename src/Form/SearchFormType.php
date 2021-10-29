<?php

namespace App\Form;

use App\Entity\Site;
use App\Utils\RechercheSortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site', EntityType::class, [ 'class'=>Site::class,
                'choice_label'=>function($site){
                    return $site->getNomSite();
                }
            ])

            ->add('recherche', SearchType::class, [
                'label' => "Le nom de la sortie contient:",
                'required' => false
            ])

            ->add('dateDebut', DateType::class, [
                'required' => true
            ])

            ->add('dateFin', DateType::class, [
                'required' => true
            ])

            ->add('organisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false
            ])

            ->add('inscrit', CheckboxType::class, [
                'label' => "Sorties auxquelles je suis inscrit/e",
                'required' => false
            ])

            ->add('pasInscrit', CheckboxType::class, [
                'label' => "Sorties auxquelles je ne suis pas inscrit/e",
                'required' => false
            ])

            ->add('sortiesPassees', CheckboxType::class, [
                'label' => "Sorties passées",
                'required' => false
            ])
        ;
    }

    //à redéfinir pour imposer un nom pour le form
    public function getBlockPrefix()
    {
        return "searchForm";
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RechercheSortie::class,
            'csrf_protection'   => false,
        ]);
    }
}

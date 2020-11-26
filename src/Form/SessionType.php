<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Entity\Formation;
use App\Form\StagiaireType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class)
            ->add('startAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('endAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('formation',EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'libelle'
            ])
            ->add('stagiaires',CollectionType::class,[
                'entry_type' => EntityType::class,
                'entry_options'=> [
                    'class'=> Stagiaire::class,
                    'choice_label' => 'nom'
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,        
                'by_reference' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}

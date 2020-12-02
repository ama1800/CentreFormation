<?php

namespace App\Form;

use App\Entity\Espace;
use App\Entity\Formation;
use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EspaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('duree', IntegerType::class)
            ->add('formation',EntityType::class, [
                'class'=>  Formation::class ,
                'attr'=> ['class'=> 'selectpicker'],
                'multiple'=> false,
                'choice_label'=> function($formation){
                   return $formation->getLibelle();
                }
            ])
            ->add('module',EntityType::class, [
                'class'=>  Module::class ,
                'attr'=> ['class'=> 'selectpicker'],
                'multiple'=> false,
                'choice_label'=> function($module){
                   return $module->getLibelle();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Espace::class,
        ]);
    }
}

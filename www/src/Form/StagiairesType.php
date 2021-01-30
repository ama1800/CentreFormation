<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class StagiairesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('stagiaires', CollectionType::class, [
            'label' => false,
            'entry_type' => EntityType::class,
            'entry_options'=>[
                'label'=> 'choisir Stagiaire:',
                'class'=> Stagiaire::class
            ],
            'allow_add' => true,
            'allow_delete'=>true,
            'by_reference' => false,
        ])
        // ->add('stagiaires',EntityType::class,[
        //     'class'=>  Stagiaire::class ,
        //     'attr'=> ['class'=> 'selectpicker'],
        //     'multiple'=> true,
        //     'choice_label'=> function($stagiaire){
        //        return $stagiaire->getNom()." ".$stagiaire->getPrenom();
        //     }
        // ]) 
        ->add('submit', SubmitType::class, [
            'label' => 'Valider',
            'attr'=> [
                'class'=> 'btn btn-primary'
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}

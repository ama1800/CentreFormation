<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class Search_Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('mots', SearchType::class, [
            'attr' => ['class' => 'btn btn-secondary my-2 my-sm-0', 'placeholder' => 'search'],
            'label'=> false,
        ])
        ->add('search', SubmitType::class, [
            'attr' => ['class' => 'btn btn-secondary my-2 my-sm-0'],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

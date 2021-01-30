<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Merci de saisir un email valid'
                ])
            ]
        ])
        ->add('nom', TextType::class)
        ->add('prenom', TextType::class)
        ->add('civilite', ChoiceType::class, [
            'required' => true,
            'attr'=> ['class'=> 'selectpicker',],
            'choices'  => [
                'MONSIEUR' =>  'NO',
                'MADAME' =>  'YES',
            ],
        ])
        ->add('dateNaissance', DateType::class, [
            'widget' => 'single_text',
        ])
        ->add('cp', TextType::class)
        ->add('commune', TextType::class,)
        ->add('adresse', TextType::class)
        ->add('submit', SubmitType::class, [
            'label' => 'Valider',
            'attr'=> [
                'class'=> 'btn btn-primary'
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

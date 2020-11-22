<?php

namespace App\Form;

use App\Entity\Stagiaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class StagiaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('cp', TextType::class)
            ->add('commune', TextType::class)
            ->add('adresse', TextType::class)
            ->add('portable',TelType::class)
            ->add('civilite', CheckboxType::class, [
                'label'    => 'Mr',
                'required' => false,
            ])
            ->add('enterAt',DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('exitAt',DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('photo', TextType::class)
            ->add('sessions',ChoiceType::class,[
                'label' => 'Stagiaire Sessions : ',
                'required' => false,
                'choices'  => $options['choices']
            ])
        ;
    }
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['choices']);

        $resolver->setDefaults(
            [
            'data_class' => Stagiaire::class,
                'choices' => [],
            ]
        );
    }
}

<?php

namespace App\Form;

use App\Entity\Stagiaire;
use App\Form\SessionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

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
            ->add('brochure', FileType::class, [
                'label' => 'Photo',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/git',
                        ],
                        'mimeTypesMessage' => 'Inserez une extension valide. Seulement(.png, .jpg, .jpeg, ou .git), maximum 1024Ko',
                    ])
                ],
            ])
            ->add('sessions',CollectionType::class,[
                'entry_type' => SessionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,        
                'by_reference' => false
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

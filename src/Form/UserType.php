<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $civilite = [0 => 'Madame', 1 => 'Monsieur'];
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un email valid'
                    ])
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Le Role : ',
                'required' => false,
                'choices'  => [
                    'EMPLOYE' =>  'ROLE_USER',
                    'SECRITAIRE' =>  'ROLE_SECRITARIAT',
                    'FORMATTEUR' =>  'ROLE_FORMATTEUR',
                    'RESPONSABLE' => 'ROLE_RESPONSABLE',
                    'ADMINISTRATEUR' =>  'ROLE_ADMINISTRATEUR',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('civilite', CheckboxType::class, [
                'label'    => 'A cocher si c\'est un homme',
                'required' => false,
            ])
            ->add('dateNaissance', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('cp', TextType::class)
            ->add('commune', TextType::class)
            ->add('adresse', TextType::class)
            
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
            ]);
    }


    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['choices']);

        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'choices' => [],
            ]
        );
    }
}

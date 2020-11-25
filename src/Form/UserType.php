<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $civilite = [0=>'Madame', 1=>'Monsieur'];
        $builder
            ->add('email', EmailType::class,[
                'constraints'=>[
                    new NotBlank([
                        'message'=> 'Merci de saisir un email valid'
                        ])
                ]
            ])
            ->add('roles',ChoiceType::class,[
                'label' => 'Le Role : ',
                'required' => false,
                'choices'  => [
                    'EMPLOYE'=>  'ROLE_USER',
                    'SECRITAIRE'=>  'ROLE_SECRITARIAT',
                    'FORMATTEUR'=>  'ROLE_FORMATTEUR',
                    'RESPONSABLE'=> 'ROLE_RESPONSABLE',
                    'ADMINISTRATEUR'=>  'ROLE_ADMINISTRATEUR',
                ],
                'expanded'=> true,
                'multiple'=> true,
            ])
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('civilite', CheckboxType::class, [
                'label'    => 'A cocher si c\'est un homme',
                'required' => false,
            ])
            ->add('dateNaissance',DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('cp', TextType::class)
            ->add('commune', TextType::class)
            ->add('adresse', TextType::class)
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
            'data_class' => User::class,
                'choices' => [],
            ]
        );
    }
}

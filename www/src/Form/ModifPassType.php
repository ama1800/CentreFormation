<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ModifPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('actuelPassword', PasswordType::class, array(
            'mapped' => false
        ))
        ->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'mapped' => false,
            'first_options' => array('label' => 'Mot de passe'),
            'second_options' => array('label' => 'Confirmation du mot de passe'),
            'constraints' => new Length([
                'min' => 6,
                'minMessage' => "Veuillez mettre plus de {{ limit }} characters",
                'max' => 16,
                'maxMessage' => "Veuillez ne pas mettre plus de {{ limit }} characters",
                
            ]),
            'required' => true
        ))
        ->add('submit', SubmitType::class, ['label'=>'Valider', 'attr'=>['class'=>'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_extra_fields' => true,
        ]);
    }
}

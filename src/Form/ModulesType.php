<?php

namespace App\Form;


use App\Form\EspaceType;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ModulesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('espaces', CollectionType::class, [
                'label' => false,
                'entry_type' => EspaceType::class,
                'entry_options'=>[
                    'label'=> 'Module et duree',
                ],
                'allow_add' => true,
                'allow_delete'=>true,
                'by_reference' => false,
            ])
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
            'data_class' => Formation::class,
        ]);
    }
}

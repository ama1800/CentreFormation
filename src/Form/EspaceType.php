<?php

namespace App\Form;

use App\Entity\Espace;
use App\Entity\Formation;
use App\Entity\Module;
use Doctrine\ORM\EntityRepository;
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
            ->add('module',EntityType::class, [
                'class'=>  Module::class ,
                'label'=> false,
                'query_builder'=> function(EntityRepository $er){
                    return $er->createQueryBuilder('m')
                    ->orderBy('m.libelle', 'ASC');
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

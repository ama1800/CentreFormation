<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\Session;
use App\Entity\Stagiaire;
use App\Entity\Formation;
use App\Form\StagiaireType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    
                    foreach ($event->getData()->getStagiaires() as $stagiaire) {
                        $stagiaire->addSession($event->getData());
                    }
                    
                }
            );
        $builder
            ->add('libelle', TextType::class)
            ->add('startAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('endAt', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('formation',EntityType::class, [
                'class' => Formation::class,
                'choice_label' => 'libelle'
            ])
            ->add('stagiaires',EntityType::class,[
                'class'=>  Stagiaire::class ,
                'attr'=> ['class'=> 'selectpicker'],
                'multiple'=> true,
                'choice_label'=> function($stagiaire){
                   return $stagiaire->getNom()." ".$stagiaire->getPrenom();
                }
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}

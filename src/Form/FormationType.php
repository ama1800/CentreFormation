<?php

namespace App\Form;

use App\Entity\Espace;
use App\Entity\Module;
use App\Form\EspaceType;
use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Session;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class)
            ->add('description', CKEditorType::class)
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'libelle'
            ])
            // ->add('sessions', EntityType::class, [
            //     'class'=>  Session::class ,
            //     'attr'=> ['class'=> 'selectpicker'],
            //     'multiple'=> true,
            //     'choice_label'=> function($session){
            //        return $session->getLibelle();
            //     }
            // ])
            ->add('brochure', FileType::class, [
                'label' => 'Photo',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                // make it optional so you don't have to re-upload the PDF file
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
                        'mimeTypesMessage' => 'Inserez une extension valide. Seulement(.png, .jpg, .jpeg, ou .git)',
                    ])
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

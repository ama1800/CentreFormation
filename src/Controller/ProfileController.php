<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile/{id}", name="profile", methods={"GET","POST"})
     */
    public function modifProfile(Request $request): Response
    {
        
        $user= $this->getUser();
        $form = $this->createFormBuilder($user)
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
            ->add('commune', TextType::class)
            ->add('adresse', TextType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('message', 'Profile à jour. Merci');
            return $this->redirectToRoute('user_index');
        }
        return $this->render('profile/index.html.twig', [
            'user'=> $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @("/profile/{id}", name="pass_profile", methods={"GET","POST"})
     */
    public function changePassProfile(Request $request): Response
    {
        $user= $this->getUser();
        $form = $this->createFormBuilder($user)
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('message', 'Profile à jour. Merci');
            return $this->redirectToRoute('user_index');
        }
        return $this->render('profile/index.html.twig', [
            'user'=> $user,
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AvatarType;
use App\Form\ProfileType;
use App\Form\ModifPassType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    
    /**
     * @Route("/modif/{id}", name="profile_modif", methods={"GET","POST"})
     */
    public function modifProfile(Request $request, SluggerInterface $slugger, UserPasswordEncoderInterface $encoder, EntityManagerInterface $entityManager): Response
    {
        $id=$this->getUser()->getId();
        $user= $this->getUser();
        $form1 = $this->createForm(ProfileType::class, $user);
            
        $form2 = $this->createForm(ModifPassType::class,$user);

        $form3 = $this->createForm(AvatarType::class, $user);



            // dd($user);
        $form1->handleRequest($request);
        if ($form1->isSubmitted() && $form1->isValid()) {
             
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('message', 'Profile à jour. Merci');
            return $this->redirectToRoute('profile_modif', ['id'=> $id]);
        }
        
           

        $form2->handleRequest($request);
        if ($form2->isSubmitted() && $form2->isValid()) 
        {
            $oldPassword = $request->request->get('modif_pass')['actuelPassword'];
            // Si l'ancien mot de passe est bon
            if ($encoder->isPasswordValid($user, $oldPassword)) 
            {
                $newEncodedPassword = $encoder->encodePassword(
                $user,
                $form2->get('plainPassword')->getData()
            );
            $user->setPassword($newEncodedPassword);
        
            $entityManager->flush();
            $this->addFlash('succes', 'Votre mot de passe à bien été changé !');
            return $this->redirectToRoute('profile_modif', ['id'=> $id]);
        } 
        else 
        {
        $form2->addError(new FormError('Ancien mot de passe incorrect'));
        }

            $this->addFlash('message', 'Profile à jour. Merci');
            return $this->redirectToRoute('profile_modif', ['id'=> $id]);
        }


        $form3->handleRequest($request);
        if ($form3->isSubmitted() && $form3->isValid()) 
        {
            // dd($user);
            // upload photo
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form3['brochure']->getData();
            
            if ($brochureFile) 
            {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $e->getMessage();
                }
                // updates the 'photo' property to store the image file name
                // instead of its contents
                $user->setPhoto($newFilename); 
            }
             $entityManager->persist($user);
             $entityManager->flush();
           
            $this->addFlash('message', 'Profile à jour. Merci');
            return $this->redirectToRoute('profile', ['id'=> $id]);
        }
        return $this->render('profile/index.html.twig', [
            'user'=> $user,
            'form1' => $form1->createView(),
            'form3' => $form3->createView(),
            'form2' => $form2->createView(),
        ]);
    }

}

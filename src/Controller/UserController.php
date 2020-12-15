<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @isGranted("ROLE_SECRITARIAT")
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @isGranted("ROLE_ADMINISTRATEUR")    
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder, \Swift_Mailer $mailer, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // upload photo
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form['brochure']->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

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

            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            
            // Generer le token d'activation de compte

            $user->setActivationToken(md5(uniqid()));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Céation du message
            $message = (new \Swift_Message('Activation de votre compte'))
                // Expediteur
                ->setFrom('votre@adresse.fr')
                // Destenataire
                ->setTo($user->getEmail())
                // Contenu
                ->setBody(
                    $this->renderView(
                        'emails/activation.html.twig',
                        ['token' => $user->getActivationToken()]
                    ),
                    'text/html'
                );
            // on envoie l'email
            $mailer->send($message);

                
        // Message flash de succes
        $this->addFlash('warning', 'Création de compte avec succes. Email d\'activation est envoyer à '.$user->getEmail().'');
            return $this->redirectToRoute('formation_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $encoder, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // upload photo
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form['brochure']->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();

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
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $this->getDoctrine()->getManager()->flush();

            // Message flash de supprission
            $this->addFlash('success', 'Compte Mise à jour avec succes.');
            return $this->redirectToRoute('formation_index');
        }


        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @isGranted("ROLE_ADMINISTRATEUR")
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        // Message flash de supprission
        $this->addFlash('danger', 'Compte supprimer avec succes.');
        return $this->redirectToRoute('formation_index');
    }
    // 
    /**
     * @Route("/activation/{token}", name="user_activation", methods={"GET","POST"})
     */
    public function activation($token, UserRepository $userRepository): Response
    {
        // verification de l'existance de token a la base de donnee
        $user = $userRepository->findOneBy(['activationToken' => $token]);

        // Si aucun user ne posséde ce token

        if (!$user) {
            // Erreur ce token n'existe pas
            throw $this->createNotFoundException('Cet Utilisateur n\'exist pas!');
        }
        // Si token existe alors on le supprime

        $user->setActivationToken(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Message flash de sucess activation
        $this->addFlash('message', 'Votre compte est activer vous pouvez y acceder et changer votre profile et mot de passe. Merci');
        return $this->redirectToRoute('formation_index');
    }
}

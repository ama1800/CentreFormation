<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) 
        {
            if($this->getUser()->getActivationToken() != null)
            {
                $this->addFlash('danger', 'Votre compte n\'est toujours pas active, veuillez consulter votre boite email et cliquer sur le lien d\'activation. Merci');
            }
            return $this->redirectToRoute('/');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.'); 
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/passoublie", name="app_forgottenPassword")
     */
    public function forgottenPassword(Request $request,UserRepository $userRepository, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        //  on cree le formulaire
        $form= $this->createForm(ResetPassType::class);

        //  traitement du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //  recupéer les donnees
            $data= $form->getData();
            // chercher la corespandance de l'email dans la BD
            $user= $userRepository->findOneByEmail($data['email']);
            if(!$user)
            {
                // Message flash
                $this->addFlash('danger','Cet email n\'existe pas');
                return $this->redirectToRoute('app_login');
            }
            // On génere un token avec le token generator
            $token= $tokenGenerator->generateToken();
            try
            {
                $user->setRestToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            }
            catch(\Exception $e)
            {
                $this->addFlash('warning', 'attention, il y a une erreur :'.$e->getMessage());
                return $this->redirectToRoute('app_login');
            }
        // envoie vers la page de saisie de l'email
        //  génerer l'adresse de reinitialisation du mot de passe
        $url= $this->generateUrl('app_reset_password', ['token'=> $token],
        UrlGeneratorInterface::ABSOLUTE_URL);
        //  envoie du message
        $message= (new \Swift_Message('Mot de passe oublier'))
        ->setFrom('no_replay@centre_formation.fr')
        ->setTo($user->getEmail())
        ->setBody('
        <span>Bonjour,</span><p> vous avez oublier votre mot de passe, vous pouvez le réinitiliser en suivant le lien si dessous.
        Le lien de réinitialisation est disponible pour une duree de 4 heures, dépasser les 4 heures vous devez refaire une autre demande pour pouvoir réinitialiser votre mot de passe.
        Cordialement.
        Pour la réinitialisation de votre mot de passe <a href="'.$url.'">Suivez ce lien</a></p>',
        'taxt/html');
        //  Envoie du message
        $mailer->send($message);
        // Message de success
         $this->addFlash('message','Un email contenant un lien de réinitialisation de votre mot passe vous a étè bien envoyer à votre boite email; si vous ne le recever pas merci de verfier votre email indésirable');
         return $this->redirectToRoute('app_login');
            
        }
        //  on envoie vers la page de demande de l'email
       return $this->render('security/forgottenPass.html.twig', ['emailForm'=> $form->createView()]);
    }
    /**
     * @Route("/resetpass", name="app_reset_password")
     */
    public function resetPassword(Request $request, $token=null, UserRepository $users, UserPasswordEncoderInterface $encoder)
    {
        
        $token = (isset($_GET['token'])) ? $_GET['token'] : null;
        if($token !== null)
        {
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['restToken'=> $token]);
            if(!$user)
                {
                    // Message flash
                    $this->addFlash('danger','Y a un probleme d\'authentification!! SVP veuillez réessayer de nouveau.');
                    return $this->redirectToRoute('app_login');
                }
            if($request->isMethod('POST'))
            {
                $user->setRestToken(null);
                $hash= $encoder->encodePassword($user, $request->request->get('password'));
                $user->setPassword($hash);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                
                $this->addFlash('message','Mot de passe modifier avec succee.');
                return $this->redirectToRoute('app_login');
            }
            else
            return $this->render('security/resetpass.html.twig', ['token'=> $token]);
        }
    }
    /**
     * @Route("/editpass", name="app_editpass")
     */
    public function changePassword(Request $request, User $user, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash= $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}

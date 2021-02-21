<?php

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Formation;
use App\Form\ModulesType;
use App\Form\SessionType;
use App\Form\StagiairesType;
use App\Repository\SessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @isGranted("ROLE_SECRITARIAT")
 * @Route("/session")
 */
class SessionController extends AbstractController
{

    /**
     * @Route("/", name="session_index", methods={"GET"})
     */
    public function index(SessionRepository $sessionRepository): Response
    {

        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/calendar", name="session_calendar", methods={"GET"})
     */
    public function calendar()
    {
        return $this->render('session/calendar.html.twig');
    }

    /**
     * @Route("/new", name="session_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session, ['validation_groups' => 'session_prop']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/new.html.twig', [
            'session' => $session,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="session_show", methods={"GET"})
     */
    public function show(Session $session): Response
    {
        return $this->render('session/show.html.twig', [
            'session' => $session,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="session_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Session $session): Response
    {
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('session_index');
        }

        return $this->render('session/edit.html.twig', [
            'session' => $session,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/ajoutStagiaires", name="add_stagiaires", methods={"GET","POST"})
     */
    public function addStagiairesToSession(Request $request, Session $session, EntityManagerInterface $em): Response
    {

        $nb = $session->getNbPlaces();
        $nbStagiaires = count($session->getStagiaires());
        $form = $this->createForm(StagiairesType::class, $session);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($nb > $nbStagiaires) {
                $em->persist($session);
                $em->flush();
                return $this->redirectToRoute('session_index');
            } else
                $this->addFlash('danger', 'La session est compléte vous ne pouvez plus ajouter de stagiaires.');
            return $this->redirectToRoute('session_show', ['id' => $session->getId()]);
        }
        return $this->render('session/addStagiaires.html.twig', [
            'form' => $form->createView(),
            'session' => $session,
        ]);
    }

    /**
     * @Route("/{id}", name="session_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Session $session): Response
    {
        if ($this->isCsrfTokenValid('delete' . $session->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($session);
            $entityManager->flush();
        }

        return $this->redirectToRoute('session_index');
    }
}

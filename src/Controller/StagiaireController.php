<?php

namespace App\Controller;

use App\Entity\Stagiaire;
use App\Form\StagiaireType;
use App\Repository\StagiaireRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @isGranted("ROLE_SECRITARIAT")
 * @Route("/stagiaire")
 */
class StagiaireController extends AbstractController
{
    /**
     * @Route("/", name="stagiaire_index", methods={"GET"})
     */
    public function index(StagiaireRepository $stagiaireRepository): Response
    {
        return $this->render('stagiaire/index.html.twig', [
            'stagiaires' => $stagiaireRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="stagiaire_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $stagiaire = new Stagiaire();
        $form = $this->createForm(StagiaireType::class, $stagiaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form['brochure']->getData();
            
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
                $stagiaire->setPhoto($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($stagiaire);
            $entityManager->flush();

            return $this->redirectToRoute('stagiaire_index');
        }

        return $this->render('stagiaire/new.html.twig', [
            'stagiaire' => $stagiaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="stagiaire_show", methods={"GET"})
     */
    public function show(Stagiaire $stagiaire): Response
    {
        return $this->render('stagiaire/show.html.twig', [
            'stagiaire' => $stagiaire,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="stagiaire_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Stagiaire $stagiaire, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(StagiaireType::class, $stagiaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $brochureFile = $form['brochure']->getData();
            
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
                $stagiaire->setPhoto($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('stagiaire_index');
        }

        return $this->render('stagiaire/edit.html.twig', [
            'stagiaire' => $stagiaire,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="stagiaire_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Stagiaire $stagiaire): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stagiaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($stagiaire);
            $entityManager->flush();
        }

        return $this->redirectToRoute('stagiaire_index');
    }
}

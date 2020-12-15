<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Espace;
use App\Entity\Module;
use App\Entity\Formation;
use App\Form\ModulesType;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/formation")
 */
class FormationController extends AbstractController
{
    /**
     * @Route("/", name="formation_index", methods={"GET"})
     */
    public function index( Request $request, PaginatorInterface $paginator): Response
    {
        // Methd findBy por réquerer les donnees avec les crétéres de filtre et de tri
        $donnees=$this->getDoctrine()->getRepository(Formation::class)->findBy([],['libelle'=>'asc']);

        $formations= $paginator->paginate(
            $donnees, // on passe les donnees
            $request->query->getInt('page',1), // numero de la page en cours, la page 1 par defaut
            4, //nombre d'elements par page.
        );
        // $pdfOptions = new Options();
        // $pdfOptions->set('defaultFont', 'Arial');
        
        // // Instantiate Dompdf with our options
        // $dompdf = new Dompdf($pdfOptions);
        
        // // Retrieve the HTML generated in our twig file
        // $html = $this->renderView('formation/index.html.twig', [
        //     'formations' => $formationRepository->findAll(),
        // ]);
        
        // // Load HTML to Dompdf
        // $dompdf->loadHtml($html);
        
        // // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        // $dompdf->setPaper('A4', 'portrait');

        // // Render the HTML as PDF
        // $dompdf->render();
        // // Store PDF Binary Data
        // $output = $dompdf->output();
        
        // // In this case, we want to write the file in the public directory
        // $publicDirectory = $this->get('kernel')->getProjectDir() . '/public';
        // // e.g /var/www/project/public/mypdf.pdf
        // $pdfFilepath =  $publicDirectory . '/mypdf.pdf';
        
        // // Write file to the desired path
        // file_put_contents($pdfFilepath, $output);
        
        // // Send some text response
        // $reponse= new Response("The PDF file has been succesfully generated !");
            
        return $this->render('formation/index.html.twig', [
            'formations' => $formations,
            // 'reponse' =>$reponse
        ]);
    }

    /**
     * @Route("/new", name="formation_new", methods={"GET","POST"})
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
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
                $formation->setPhoto($newFilename);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('formation_index');
        }

        return $this->render('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="formation_show", methods={"GET"})
     */
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }

    /**
     * @isGranted("ROLE_ADMINISTRATEUR")
     * @Route("/{id}/edit", name="formation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Formation $formation, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
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
                $formation->setPhoto($newFilename);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('formation_index');
        }

        return $this->render('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/ajoutDuree", name="add_duree", methods={"GET","POST"})
     */
    public function addModuleToFormation(Request $request, Formation $formation, EntityManagerInterface $em): Response
    {
        $form= $this->createForm(ModulesType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($formation);
            $em->flush();

            return $this->redirectToRoute('formation_index');
        }
        return $this->render('espace/addDuree.html.twig', [
            'form'=> $form->createView(),
            'formation'=> $formation,
        ]);
    }

    /**
     * @isGranted("ROLE_ADMINISTRATEUR")
     * @Route("/{id}", name="formation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Formation $formation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$formation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('formation_index');
    }
    
    

}


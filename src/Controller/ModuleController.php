<?php

namespace App\Controller;

use App\Entity\Module;
use App\Form\ModuleType;
use App\Repository\ModuleRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/module")
 */
class ModuleController extends AbstractController
{
    /**
     * @isGranted("ROLE_SECRITARIAT")
     * @Route("/", name="module_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {// Methd findBy por réquerer les donnees avec les crétéres de filtre et de tri
        $donnees=$this->getDoctrine()->getRepository(Module::class)->findBy([],['libelle'=>'asc']);

        $modules= $paginator->paginate(
            $donnees, // on passe les donnees
            $request->query->getInt('page',1), // numero de la page en cours, la page 1 par defaut
            4, //nombre d'elements par page.
        );
        return $this->render('module/index.html.twig', [
            'modules' => $modules,
        ]);
    }

    /**
     * @isGranted("ROLE_RESPONSABLE")
     * @Route("/new", name="module_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $module = new Module();
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($module);
            $entityManager->flush();

            return $this->redirectToRoute('module_index');
        }

        return $this->render('module/new.html.twig', [
            'module' => $module,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="module_show", methods={"GET"})
     */
    public function show(Module $module): Response
    {
        return $this->render('module/show.html.twig', [
            'module' => $module,
        ]);
    }

    /**
     * @isGranted("ROLE_RESPONSABLE")
     * @Route("/{id}/edit", name="module_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Module $module): Response
    {
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('module_index');
        }

        return $this->render('module/edit.html.twig', [
            'module' => $module,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @isGranted("ROLE_RESPONSABLE")
     * @Route("/{id}", name="module_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Module $module): Response
    {
        if ($this->isCsrfTokenValid('delete'.$module->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($module);
            $entityManager->flush();
        }

        return $this->redirectToRoute('module_index');
    }
}

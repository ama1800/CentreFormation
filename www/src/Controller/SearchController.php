<?php

namespace App\Controller;

use App\Form\Search_Type;
use App\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Extensions\Doctrine\MatchAgainst;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search_bar")
     */
    public function searchBar(Request $request,FormationRepository $formationRepository): Response
    {
        $formations= $formationRepository->findAll();
        $form = $this->createFormBuilder()
        ->add('mots', SearchType::class, [
            'attr' => ['class' => 'btn btn-secondary my-2 my-sm-0', 'placeholder' => 'search'],
            'label'=> false,
        ])
        ->add('search', SubmitType::class, [
            'attr' => ['class' => 'btn btn-secondary my-2 my-sm-0'],
        ])
        ->getForm();
        $search= $form->handleRequest($request);
        if($form->isSubmitted() && $form->isSubmitted())
        {
            //on recherche les formations correspondant au mots clés
            $formations= $formationRepository->search($search->get('mots')->getData());
        }

        return $this->render('search/searchBar.html.twig', [
            'formations' => $formations,
            'form'=>$form->createView(),
        ]);
    }
    // /**
    //  * @Route("/search", name="search", methods={"GET", "POST"})
    //  */
    // public function search(Request $request,FormationRepository $formationRepository )
    // {
    //     // $formations= $formationRepository->findAll();
    //     $form = $this->createForm(Search_Type::class);
    //     $search= $form->handleRequest($request);
    //     if($form->isSubmitted() && $form->isSubmitted())
    //     {
    //         //on recherche les formations correspondant au mots clés
    //         $formations= $formationRepository->search($search->get('mots')->getData());
    //     }

    //     return $this->render('search/searchBar.html.twig', [
    //         'formations' => $formations,
    //         'form'=>$form->createView()
    //     ]);
    // }
}

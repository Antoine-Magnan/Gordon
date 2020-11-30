<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Recette;
use App\Form\CommentType;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecettesController extends AbstractController
{
    /**
     * @Route("/recettes", name="recettes")
     */
    public function index(RecetteRepository $repo): Response
    {
        $recettes = $repo->findAll();

        return $this->render('recettes/index.html.twig', [
            'controller_name' => 'RecettesController',
            'recettes' => $recettes
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(RecetteRepository $repo){
        return $this->render('recettes/home.html.twig',[
            'controller_name' => 'RecettesController',
            'recettes' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/recettes/new", name="recette_create")
     * @Route("recettes/{id}/edit", name="recette_edit")
     */
    public function form(Recette $recette = null, Request $request, EntityManagerInterface $manager){

        if(!$recette){
            $recette = new Recette();
        }
        
        $form = $this->createForm(RecetteType::class, $recette);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$recette->getId()){
                $recette->setCreatedAt(new \DateTime());
            }

            $manager->persist($recette);
            $manager->flush();

            return $this->redirectToRoute('recette_complete', ['id' => $recette->getId()]);
        }

        return $this->render('recettes/create.html.twig',[
            'formRecette' => $form->createView(),
            'editMode' => $recette->getId() !==null
        ]);
    }

    /**
     * @Route("/recettes/{id}", name="recette_complete")
     */
    public function show(Recette $recette, Request $request, EntityManagerInterface $manager){
        $comment= new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setRecette($recette);
            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('recette_complete', ['id'=>$recette->getId()]);
        }

        return $this->render('recettes/show.html.twig',[
            'recette' =>$recette,
            'commentForm' => $form->createView()
        ]);
    }

}

<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/main", name="main")
     */
    public function index(RecipeRepository $repo): Response
    {
        $recipes = $repo->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'recipes' => $recipes
        ]);
    }
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('main/home.html.twig');
    }
    /**
     * @Route ("/main/new", name="create")
     * @Route("/main/{id}/edit", name="main_edit")
     */
    public function form(Recipe $recipe = null, Request $request, EntityManagerInterface  $manager)
    {
        if (!$recipe) {
            $recipe = new Recipe();
        }
        
        $form = $this->createForm(RecipeType::class, $recipe);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$recipe->getId()) {
                $recipe->setCreatedAt(new \DateTime());
            }

            $manager->persist($recipe);
            $manager->flush();

            return $this->redirectToRoute('main_show', ['id' => $recipe->getId()]);
        }

        return $this->render('main/create.html.twig', [
            'formRecipe' => $form->createView(),
            'editMode' => $recipe->getId() !== null
        ]); 
    }
    /**
     * @Route ("main/show/{id}", name="main_show")
     */
    public function show(Recipe $recipe)
    {
        return $this->render('main/show.html.twig', [
            'recipe'=> $recipe
        ]);
    }
}

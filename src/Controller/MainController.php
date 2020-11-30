<?php

namespace App\Controller;


use App\Entity\Recipe;
use App\Entity\Comment;
use App\Entity\Ingredient;
use App\Entity\SearchRecipe;
use App\Form\RecipeType;
use App\Form\SearchType;
use App\Form\CommentType;
use App\Form\SearchRecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    public function home(RecipeRepository $repo)
    {
        $recipes = $repo->findAll();
        return $this->render('main/home.html.twig', [
            'recipes' => $recipes
        ]);
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
            if (!$recipe->getAuthor()) {
                $recipe->setAuthor($this->getUser()->getUsername());
                $recipe->setUser($this->getUser());
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
    public function show(Recipe $recipe, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime())
                ->setRecipe($recipe);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('main_show', ['id' => $recipe->getId()]);
        }

        return $this->render('main/show.html.twig', [
            'recipe' => $recipe,
            'commentForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function recherche(Request $request, RecipeRepository $repo, PaginatorInterface $paginator)
    {
        $search = new SearchRecipe();
        $searchForm = $this->createForm(SearchRecipeType::class,$search);
        $searchForm->handleRequest($request);


        

        // Paginate the results of the query
        $recipes = $paginator->paginate(
            // Doctrine Query, not results
            $repo->findBySearch($search),
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );
        return $this->render('main/search.html.twig', [
            'recipes' => $recipes,
            'searchForm' => $searchForm->createView()
        ]);
    }
}

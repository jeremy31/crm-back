<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route("/recipe")
 */
class RecipeController extends AbstractController
{
    /**
     * @Route("/", name="recipe_index", methods="GET")
     */
    public function index(RecipeRepository $recipeRepository): Response
    {
        $stock = 0;
        $valorization = 0;

        foreach($recipeRepository->findAll() as $recipe)
        {
            $stock += $recipe->getStock();
            $valorization += $recipe->getValorization();
        }

        $vars = array(
            'recipes' => $recipeRepository->findBy(array(), array('active' => 'DESC', 'name' => 'ASC')),
            'stock' => $stock,
            'valorization' => $valorization,
        );

        return $this->render('recipe/index.html.twig', $vars);
    }

    /**
     * @Route("/impression", name="recipe_impression", methods="GET")
     */
    public function impression(RecipeRepository $recipeRepository): Response
    {
        $recipes = $recipeRepository->findBy(array('active' => true), array('name' => 'ASC'));
        $recipesOrdered = $this->getRecipesOrderByPrimary($recipes);

        ksort($recipesOrdered);

        $vars = array(
            'recipesOrdered' => $recipesOrdered
        );

        return $this->render('recipe/impression.html.twig', $vars);
    }

    /**
     * @return array
     */
    public function getRecipesOrderByPrimary($recipesList): array
    {
        $recipes = array();

        foreach ($recipesList as $key => $recipe) {
            if($recipe->getPrimary() AND $recipe->getPrimary()->getProduct()->getProduct()->getId() == 53)
                $recipes['Thé vert de Chine'][] = $recipe;
            elseif($recipe->getPrimary())
                $recipes[$recipe->getPrimary()->getProduct()->getProduct()->getName()][] = $recipe;
        }

        return $recipes;
    }

    /**
     * @Route("/export_prestashop", name="recipe_export_prestashop", methods="GET")
     */
    public function exportPrestashop(RecipeRepository $recipeRepository): Response
    {
        $response = new StreamedResponse();
        $response->setCallback(function() use ($recipeRepository) {
            $handle = fopen('php://output', 'w+');

            fputcsv($handle, ['id', 'active', 'name', 'price', 'weight', 'stock'], ';');
            $results = $recipeRepository->findBy(array(), array('active' => 'DESC', 'name' => 'ASC'));

            foreach ($results as $user) {
                if($user->getPrestashop())
                {
                    fputcsv(
                        $handle,
                        [$user->getPrestashop(), (int)$user->getActive(), $user->getName(), $user->getPrice(), $user->getWeight(), $user->getStock()],
                        ';'
                     );
                }
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="export-prestashop.csv"');

        return $response;
    }

    /**
     * @Route("/new", name="recipe_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recipe);
            $em->flush();

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('recipe/new.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recipe_show", methods="GET")
     */
    public function show(Recipe $recipe): Response
    {
        return $this->render('recipe/show.html.twig', ['recipe' => $recipe]);
    }

    /**
     * @Route("/{id}/edit", name="recipe_edit", methods="GET|POST")
     */
    public function edit(Request $request, Recipe $recipe): Response
    {
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render('recipe/edit.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recipe_delete", methods="DELETE")
     */
    public function delete(Request $request, Recipe $recipe): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipe->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($recipe);
            $em->flush();
        }

        return $this->redirectToRoute('recipe_index');
    }
}

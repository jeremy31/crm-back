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
            $stock += $recipe->getStock(20);
            $stock += $recipe->getStock(45);
            $stock += $recipe->getStock(70);
            $stock += $recipe->getStock(150);
            $valorization += $recipe->getValorization(20);
            $valorization += $recipe->getValorization(45);
            $valorization += $recipe->getValorization(70);
            $valorization += $recipe->getValorization(150);
        }

		$sort = 'name';
		
		if(isset($_GET['sort']))
			$sort = $_GET['sort'];
			
        $vars = array(
            'recipes' => $recipeRepository->findBy(array(), array('active' => 'DESC', 'weight' => 'DESC', $sort => 'ASC')),
            'stock' => $stock,
            'valorization' => $valorization,
        );

        return $this->render('recipe/index.html.twig', $vars);
    }
	
    /**
     * @Route("/stocks", name="recipe_stocks", methods="GET")
     */
    public function stocks(RecipeRepository $recipeRepository): Response
    {
        $vars = array(
            'recipes_classic' => $recipeRepository->findBy(array('weight' => 70), array('active' => 'DESC', 'name' => 'ASC')),
            'recipes_others' => $recipeRepository->findBy(array('weight' => 0), array('active' => 'DESC', 'name' => 'ASC')),
        );
		
        return $this->render('recipe/stocks.html.twig', $vars);
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
		$results = $recipeRepository->findBy(array(), array('active' => 'DESC', 'name' => 'ASC'));

		$list = array();
		
		foreach ($results as $recipe)
		{
			$declensions = array();
			if($recipe->getWeight() != 70)
			{
				$price = $recipe->getPrice();
				$weight = $recipe->getWeight();
				$stock = $recipe->getStock();
			}
			else
			{
				$price = 0;
				$weight = 0;
				$stock = 0;
				
				$declensions[] = array('name' => 'Sachet de 70g', 'price' => $recipe->getPrice(70), 'weight' => 70, 'stock' => $recipe->getStock(70));
				$declensions[] = array('name' => 'Boîte de 20g', 'price' => $recipe->getPrice(20), 'weight' => 20, 'stock' => $recipe->getStock(20));
				$declensions[] = array('name' => 'Boîte de 45g', 'price' => $recipe->getPrice(45), 'weight' => 45, 'stock' => $recipe->getStock(45));
				$declensions[] = array('name' => 'Boîte de 150g', 'price' => $recipe->getPrice(150), 'weight' => 150, 'stock' => $recipe->getStock(150));
			}
			$list[] = array(
				'id' => $recipe->getPrestashop(), 
				'active' => (int)$recipe->getActive(), 
				'name' => $recipe->getName(), 
				'title' => $recipe->getTitle(), 
				'description' => $recipe->getDescription(), 
				'price' => $price, 
				'weight' => $weight, 
				'stock' => $stock,
				'declensions' => $declensions
			);
		}

		$url ='https://www.le-chateau-en-the.com/import';
		
		$postdata = http_build_query(
			array(
				'token' => '768J793HJGH',
				'data' => $list
			)
		);

		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $postdata
			)
		);

		$context  = stream_context_create($opts);
		$result = file_get_contents($url, false, $context);
		
		$result = (array)json_decode($result);
		
        return $this->render('recipe/export.html.twig', $result);
    }

    /**
     * @Route("/export_prestashop_declension", name="recipe_export_prestashop_declension", methods="GET")
     */
    public function exportPrestashopDeclension(RecipeRepository $recipeRepository): Response
    {
        $response = new StreamedResponse();
        $response->setCallback(function() use ($recipeRepository) {
            $handle = fopen('php://output', 'w+');

            fputcsv($handle, ['id', 'attribut', 'valeur', 'price', 'weight', 'stock'], ';');
            $results = $recipeRepository->findBy(array(), array('active' => 'DESC', 'name' => 'ASC'));

            foreach ($results as $recipe) {
                if($recipe->getPrestashop() AND $recipe->getWeight() == 70)
                {
                    fputcsv(
                        $handle,
                        [$recipe->getPrestashop(), 'Conditionnement:color', 'Sachet de 70g', $recipe->getPrice(70), 70, $recipe->getStock(70)],
                        ';'
                     );
                    fputcsv(
                        $handle,
                        [$recipe->getPrestashop(), 'Conditionnement:color', 'Boîte de 20g', $recipe->getPrice(20), 20, $recipe->getStock(20)],
                        ';'
                     );
                    fputcsv(
                        $handle,
                        [$recipe->getPrestashop(), 'Conditionnement:color', 'Boîte de 45g', $recipe->getPrice(45), 45, $recipe->getStock(45)],
                        ';'
                     );
                    fputcsv(
                        $handle,
                        [$recipe->getPrestashop(), 'Conditionnement:color', 'Boîte de 150g', $recipe->getPrice(150), 150, $recipe->getStock(150)],
                        ';'
                     );
                }
            }

            fclose($handle);
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition','attachment; filename="export-prestashop-declension.csv"');

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
     * @Route("/{id}/edit-stock/{weight}/{order}", name="recipe_edit_stock", methods="GET|POST")
     */
    public function editStock(Request $request, Recipe $recipe, $weight, $order): Response
    {
		$stock = $recipe->getStock($weight) + ($order == 'm' ? - 1 : 1);
		
		if($weight == 20)
			$recipe->setStock20($stock);
		elseif($weight == 45)
			$recipe->setStock45($stock);
		elseif($weight == 150)
			$recipe->setStock150($stock);
		else
			$recipe->setStock($stock);
		
        $this->getDoctrine()->getManager()->flush();

        return new Response($stock);
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

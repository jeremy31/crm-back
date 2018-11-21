<?php

namespace App\Controller;

use App\Entity\RecipeProduct;
use App\Form\RecipeProductType;
use App\Repository\RecipeProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/recipe/product")
 */
class RecipeProductController extends AbstractController
{
    /**
     * @Route("/", name="recipe_product_index", methods="GET")
     */
    public function index(RecipeProductRepository $recipeProductRepository): Response
    {
        return $this->render('recipe_product/index.html.twig', ['recipe_products' => $recipeProductRepository->findAll()]);
    }

    /**
     * @Route("/new", name="recipe_product_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $recipeProduct = new RecipeProduct();
        $form = $this->createForm(RecipeProductType::class, $recipeProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recipeProduct);
            $em->flush();

            return $this->redirectToRoute('recipe_product_index');
        }

        return $this->render('recipe_product/new.html.twig', [
            'recipe_product' => $recipeProduct,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recipe_product_show", methods="GET")
     */
    public function show(RecipeProduct $recipeProduct): Response
    {
        return $this->render('recipe_product/show.html.twig', ['recipe_product' => $recipeProduct]);
    }

    /**
     * @Route("/{id}/edit", name="recipe_product_edit", methods="GET|POST")
     */
    public function edit(Request $request, RecipeProduct $recipeProduct): Response
    {
        $form = $this->createForm(RecipeProductType::class, $recipeProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('recipe_product_edit', ['id' => $recipeProduct->getId()]);
        }

        return $this->render('recipe_product/edit.html.twig', [
            'recipe_product' => $recipeProduct,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="recipe_product_delete", methods="DELETE")
     */
    public function delete(Request $request, RecipeProduct $recipeProduct): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recipeProduct->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($recipeProduct);
            $em->flush();
        }

        return $this->redirectToRoute('recipe_product_index');
    }
}

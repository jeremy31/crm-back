<?php

namespace App\Controller;

use App\Entity\ProductDeclension;
use App\Form\ProductDeclensionType;
use App\Repository\ProductDeclensionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product/declension")
 */
class ProductDeclensionController extends AbstractController
{
    /**
     * @Route("/", name="product_declension_index", methods="GET")
     */
    public function index(ProductDeclensionRepository $productDeclensionRepository): Response
    {
        return $this->render('product_declension/index.html.twig', ['product_declensions' => $productDeclensionRepository->findAll()]);
    }

    /**
     * @Route("/new", name="product_declension_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $productDeclension = new ProductDeclension();
        $form = $this->createForm(ProductDeclensionType::class, $productDeclension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productDeclension);
            $em->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product_declension/new.html.twig', [
            'product_declension' => $productDeclension,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_declension_show", methods="GET")
     */
    public function show(ProductDeclension $productDeclension): Response
    {
        return $this->render('product_declension/show.html.twig', ['product_declension' => $productDeclension]);
    }

    /**
     * @Route("/{id}/edit", name="product_declension_edit", methods="GET|POST")
     */
    public function edit(Request $request, ProductDeclension $productDeclension): Response
    {
        $form = $this->createForm(ProductDeclensionType::class, $productDeclension);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product_declension_edit', ['id' => $productDeclension->getId()]);
        }

        return $this->render('product_declension/edit.html.twig', [
            'product_declension' => $productDeclension,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_declension_delete", methods="DELETE")
     */
    public function delete(Request $request, ProductDeclension $productDeclension): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productDeclension->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productDeclension);
            $em->flush();
        }

        return $this->redirectToRoute('product_declension_index');
    }
}

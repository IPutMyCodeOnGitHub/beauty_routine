<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Services\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/profile/expert")
 */
class ProductController extends AbstractController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @Route("/product", name="product")
     */
    public function listProducts(): Response
    {
        $expert = $this->getUser();
        if (!$expert) {
            throw $this->createNotFoundException('The expert does not exist');
        }
        return $this->render('product/list.html.twig', [
            'expert' => $expert,
        ]);
    }

    /**
     * @Route("/product/create", name="product.create")
     */
    public function createProduct(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->productService->createProductForm($form, $product);
            if ($result) {
                $this->addFlash('success', 'Product added!');
            } else {
                $this->addFlash('danger', 'Product was not added.');
            }
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Services\ProductService;
use App\Services\ProductTagService;
use App\Services\ProductTypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/profile/expert")
 */
class ProductController extends AbstractController
{
    private $productService,
            $productTagService,
            $productTypeService;

    public function __construct(
        ProductService $productService,
        ProductTagService $productTagService,
        ProductTypeService $productTypeService)
    {
        $this->productService = $productService;
        $this->productTagService = $productTagService;
        $this->productTypeService = $productTypeService;
    }

    /**
     * @Route("/product", name="expert.product")
     */
    public function listProducts(Request $request): Response
    {
        $type = $request->query->get('type');
        $productName = $request->query->get('productName');
        $selectedTags = $request->query->get('tag');

        if ($selectedTags){
            $selectedTags = $this->productTagService->getSelectedTags($selectedTags);
        }

        if ($type || $productName) {
            $request->query->remove('page');
            $type = $this->productTypeService->getOneType($type);
        }

        $products = $this->productService->search($type, $productName, $selectedTags);
        $types = $this->productTypeService->getAllTypes();
        $tags = $this->productTagService->getAllTags();
        //$brands = $this->productService->getAllBrands();
        //$countries = $this->productService->getAllCountries();

        return $this->render('product/list.html.twig', [
            'products' => $products,
            'types' => $types,
            'tags' => $tags,
            'brands' => $selectedTags,
            //'countries' => $countries,
            //'brands' => $searchTags,
        ]);
    }

    /**
     * @Route("/product/create", name="expert.product.create")
     */
    public function createProduct(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        $expert = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->productService->createProductForm($form, $product, $expert);
            if ($result) {
                $this->addFlash('success', 'Product added!');
                return $this->redirectToRoute('expert.product');
            } else {
                $this->addFlash('danger', 'Product was not added.');
            }
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/product/{id}/edit", name="expert.product.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $product = $this->productService->findProductById($id);

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->productService->editProduct($form, $product, $request);
            if ($result) {
                $this->addFlash('success', 'Product updated!');
                return $this->redirectToRoute('expert.product');
            } else {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }

        return $this->render('product/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @Route("/product/{id}/show", name="expert.product.show")
     */
    public function showProduct(Request $request, int $id): Response
    {
        $product = $this->productService->findProductById($id);

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/product/{id}/delete", name="expert.product.delete")
     */
    public function delete(int $id, Request $request): Response
    {
        $response = $this->productService->deleteProductById($id);
        $types = $this->productTypeService->getAllTypes();

        $products = $this->productService->getAllProducts();
        return $this->render('product/list.html.twig', [
            'products' => $products,
            'types' => $types,
        ]);
    }
}

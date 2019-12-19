<?php

namespace App\Controller;

use App\Entity\ProductType;
use App\Form\ProductTypeFormType;
use App\Services\ProductTypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/expert/product")
 */
class ProductTypeController extends AbstractController
{
    private $productTypeService;

    public function __construct(ProductTypeService $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    /**
     * @Route("/type", name="expert.product.type")
     */
    public function listProductTypes()
    {
        $expert = $this->getUser();
        if (!$expert) {
            throw $this->createNotFoundException('The expert does not exist');
        }

        return $this->render('product-type/list.html.twig', [
            'expert' => $expert,
        ]);
    }

    /**
     * @Route("/type/create", name="expert.product.type.create")
     */
    public function createProductType(Request $request): Response
    {
        $productType = new ProductType();

        $form = $this->createForm(ProductTypeFormType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->productTypeService->createProductTypeForm($form, $productType);
            if ($result) {
                $this->addFlash('success', 'Product type added!');
                $this->redirectToRoute('profile.expert');
            } else {
                $this->addFlash('danger', 'Product type was not added.');
            }
        }

        return $this->render('product-type/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\ProductTag;
use App\Form\ProductTagFormType;
use App\Services\ProductTagService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/expert/product")
 */
class ProductTagController extends AbstractController
{
    private $productTagService;
    public function __construct(ProductTagService $productTagService)
    {
        $this->productTagService = $productTagService;
    }


    /**
     * @Route("/tag", name="product.tag")
     */
    public function listProductTags()
    {
        $expert = $this->getUser();
        if (!$expert) {
            throw $this->createNotFoundException('The expert does not exist');
        }

        return $this->render('product-tag/list.html.twig', [
            'expert' => $expert,
        ]);
    }

    /**
     * @Route("/tag/create", name="expert.product.tag.create")
     */
    public function createProductTag(Request $request): Response
    {
        $productTag = new ProductTag();

        $form = $this->createForm(ProductTagFormType::class, $productTag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->productTagService->createProductTagForm($form, $productTag);
            if ($result) {
                $this->addFlash('success', 'Product type added!');
                return $this->redirectToRoute('profile.expert');
            } else {
                $this->addFlash('danger', 'Product type was not added.');
            }
        }

        return $this->render('product-tag/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

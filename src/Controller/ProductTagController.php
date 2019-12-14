<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/expert/product")
 */
class ProductTagController extends AbstractController
{
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
}

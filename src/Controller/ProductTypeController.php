<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/expert/product")
 */
class ProductTypeController extends AbstractController
{
    /**
     * @Route("/type", name="product.type")
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
}

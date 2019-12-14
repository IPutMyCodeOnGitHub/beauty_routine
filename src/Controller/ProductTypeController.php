<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductTypeController extends AbstractController
{
    /**
     * @Route("/product/type", name="product_type")
     */
    public function index()
    {
        return $this->render('product_type/index.html.twig', [
            'controller_name' => 'ProductTypeController',
        ]);
    }
}

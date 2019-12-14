<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProductTagController extends AbstractController
{
    /**
     * @Route("/product/tag", name="product_tag")
     */
    public function index()
    {
        return $this->render('product_tag/index.html.twig', [
            'controller_name' => 'ProductTagController',
        ]);
    }
}

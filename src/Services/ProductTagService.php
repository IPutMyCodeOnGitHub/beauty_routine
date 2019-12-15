<?php


namespace App\Services;


use App\Entity\ProductTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\RouterInterface;

class ProductTagService
{
    private $entityManager,
        $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $router)
    {
        $this->entityManager = $em;
        $this->router = $router;
    }

    public function createProductTagForm(Form $form, ProductTag $productTag): ?ProductTag
    {
        $this->entityManager->persist($productTag);
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return null;
        }
        return $productTag;
    }
}
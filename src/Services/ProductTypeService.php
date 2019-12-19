<?php


namespace App\Services;


use App\Entity\Product;
use App\Entity\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\RouterInterface;

class ProductTypeService
{
    private $entityManager,
        $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $router)
    {
        $this->entityManager = $em;
        $this->router = $router;
    }

    public function createProductTypeForm(Form $form, ProductType $productType): ?ProductType
    {
        $this->entityManager->persist($productType);
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            return null;
        }
        return $productType;
    }

    public function getAllTypes(): array
    {
        $productTypes = $this->entityManager->getRepository(ProductType::class)->findAll();
        if (!$productTypes) {
            return [];
        }
        return $productTypes;
    }

    public function getOneType(string $type): ?ProductType
    {
        $type = $this->entityManager->getRepository(ProductType::class)->find($type);
        return $type;
    }
}
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

    public function getAllTags()
    {
        $productTags = $this->entityManager->getRepository(ProductTag::class)->findAll();
        if (!$productTags) {
            return [];
        }
        return $productTags;
    }

    public function getSelectedTags(array $tags)
    {
        $productTags = [];
        foreach ($tags as $tag) {
            $tag = $this->entityManager->getRepository(ProductTag::class)->find($tag);
            $productTags[] = $tag;
        }
        return $productTags;
    }


}
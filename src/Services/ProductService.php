<?php


namespace App\Services;


use App\Entity\Product;
use App\Entity\ProductTag;
use App\Entity\ProductType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class ProductService
{
    private $entityManager,
            $router,
            $uploaderHelper;

    public function __construct(
        EntityManagerInterface $em,
        RouterInterface $router,
        UploaderHelper $uploaderHelper
    )
    {
        $this->entityManager = $em;
        $this->uploaderHelper = $uploaderHelper;
        $this->router = $router;
    }

    public function createProductForm(Form $form, Product $product, User $user): ?Product
    {
        $photo = $form['photo']->getData();
        $photoName = $this->uploaderHelper->uploadFile($photo, UploaderHelper::PRODUCT_PHOTO_PATH);

        if ($photo) {
            $product->setPhoto(UploaderHelper::PRODUCT_PHOTO_PATH . $photoName);
        }
        $product->setExpert($user);

        $this->entityManager->persist($product);
        try {
            $this->entityManager->flush();
            return $product;
        } catch (\Exception $e) {
            if ($photoName != '') {
                $this->uploaderHelper->deleteFile($photoName, UploaderHelper::PRODUCT_PHOTO_PATH);
            }
            return null;
        }
    }

    public function getAllProducts(): array
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        if (!$products) {
            return [];
        }
        return $products;
    }


    public function findProductById(int $id): ?Product
    {
        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            new Exception('The product does not exist');
        }
        return $product;
    }

    public function editProduct(Form $form, Product $product, Request $request): ?Product
    {
            $photo = $form['photo']->getData();
            if ($photo) {
                $photoName = $this->uploaderHelper->uploadFile($photo, UploaderHelper::PRODUCT_PHOTO_PATH);
                if ($product->getPhoto()){
                    $this->uploaderHelper->deleteFile($product->getPhoto(), '');
                }
                $product->setPhoto(UploaderHelper::PRODUCT_PHOTO_PATH . $photoName);
            }

            $this->entityManager->persist($product);
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                if (isset($photoName)) {
                    $this->uploaderHelper->deleteFile($photoName, UploaderHelper::PRODUCT_PHOTO_PATH);
                }
                return null;
            }
        return $product;
    }

    public function deleteProductById(int $id): Response
    {
        $product = $this->findProductById($id);
        if (!$product) {
            return new Response(0);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return new Response(1);
    }

    public function search(?ProductType $type, ?string $productName, ?array $tags, int $page = 1): SlidingPagination
    {
        $products = $this->entityManager
            ->getRepository(Product::class)
            ->searchProductForDay($type, $productName, $tags, $page);

        return $products;
    }

    public function getAllBrands()
    {
        $brands =$this->entityManager
            ->getRepository(Product::class)
            ->selectAllBrands();

        return $brands;
    }

    public function getAllCountries()
    {
        $countries = $this->entityManager
            ->getRepository(Product::class)
            ->selectAllCountries();

        return $countries;
    }
}
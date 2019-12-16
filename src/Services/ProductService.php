<?php


namespace App\Services;


use App\Entity\Product;
use App\Entity\Routine;
use App\Entity\User;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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

   /* public function editProduct($form, int $id)
    {
        /** @var Product $product *
        $product = $this->entityManager->getRepository(Routine::class)->find($id);

        if (!$product) {
            throw new Exception('The product does not exist');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $routineService->editRoutine($form, $routine);
            if ($result) {
                $this->addFlash('success', 'Routine updated!');
            } else {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }
    }*/
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
        if ($form->isSubmitted() && $form->isValid()) {
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

            if ($product) {
                $request->getSession()->getFlashBag()->add('success', 'Product updated!');
            } else {
                $request->getSession()->getFlashBag()->add('danger', 'Sorry, that was an error.');
            }
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
}
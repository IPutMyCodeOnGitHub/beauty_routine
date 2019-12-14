<?php


namespace App\Services;


use App\Entity\Product;
use App\Entity\Routine;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\RouterInterface;

class ProductService
{
    private $entityManager,
            $router,
            $uploaderHelper;

    public function __construct(EntityManagerInterface $em, RouterInterface $router, UploaderHelper $uploaderHelper)
    {
        $this->entityManager = $em;
        $this->uploaderHelper = $uploaderHelper;
        $this->router = $router;
    }

    public function createProductForm(Form $form, Product $product): ?Product
    {
        $photo = $form['photo']->getData();
        $photoName = $this->uploaderHelper->uploadFile($photo, UploaderHelper::PRODUCT_PHOTO_PATH);

        if ($photo) {
            $product->setPhoto(UploaderHelper::PRODUCT_PHOTO_PATH . $photoName);
        }

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
}
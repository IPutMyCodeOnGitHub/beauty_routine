<?php


namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class UserService
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

    public function editExpertProfile(Form $form, User $expert, Request $request): ?User
    {
            $photo = $form['photo']->getData();
            if ($photo) {
                $photoName = $this->uploaderHelper->uploadFile($photo, UploaderHelper::USER_PHOTO_PATH);
                if ($expert->getPhoto()){
                    $this->uploaderHelper->deleteFile($expert->getPhoto(), '');
                }
                $expert->setPhoto(UploaderHelper::USER_PHOTO_PATH . $photoName);
            }

            $this->entityManager->persist($expert);
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                if (isset($photoName)) {
                    dd($photoName);
                    $this->uploaderHelper->deleteFile($photoName, UploaderHelper::USER_PHOTO_PATH);
                }
                return null;
            }
        return $expert;
    }
}
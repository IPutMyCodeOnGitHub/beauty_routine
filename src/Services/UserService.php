<?php


namespace App\Services;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function makeExpertValid($expertId, $entityManager):void
    {
        if ($expertId) {
            /**
             * @var User $user
             */
            $user = $entityManager
                ->getRepository(User::class)
                ->find($expertId);
            if ($user == null) {
                return;
            }
            $user->setRoles(['ROLE_VALID_EXPERT']);
            $entityManager->flush();
        }
    }

    public function saveForm(Form $form, User $user, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper):void
    {
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        if (array_key_exists('certificate', $form->all())) {
            if ($form['certificate']->getData() !== null) {
                $certificate = $form['certificate']->getData();
                $uploaderHelper->uploadCertificatePDF($certificate);
            }
        }

        $this->manager->persist($user);
        try {
            $this->manager->flush();
            $uploaderHelper->renameDirСertificate($user->getId());
        } catch(\Exception $e) {
            $uploaderHelper->deleteDirСertificate();
        }

    }

}
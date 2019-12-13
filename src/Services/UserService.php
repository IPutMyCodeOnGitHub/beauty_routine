<?php


namespace App\Services;

use App\Entity\User;
use App\Entity\UserCertificate;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function makeExpertValid(User $user): int
    {
        if ($user != null) {
            $user->setRoles([User::ROLE_EXPERT]);
            $this->manager->flush();
            return $user->getId();
        }
        return 0;
    }

    public function saveForm(Form $form, User $user, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper):void
    {
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        $newFilename = "";
        $userCertificate = null;
        if (array_key_exists('certificate', $form->all()) && in_array(User::ROLE_INVALID_EXPERT, $user->getRoles())) {
            if ($form['certificate']->getData() !== null) {
                $certificate = $form['certificate']->getData();
                $newFilename = $uploaderHelper->uploadFile($certificate, UploaderHelper::CERTIFICATE_PATH);
                $userCertificate = new UserCertificate();
                $userCertificate->setCertificate('certificate/'. $newFilename);
            }
        }

        $this->manager->persist($user);
        try {
            $this->manager->flush();
            if (($newFilename != '') && ($userCertificate != null)) {
                $userCertificate->setUser($user);
                $this->manager->persist($userCertificate);
                    $this->manager->flush();
            }
        } catch(\Exception $e) {
            if ($userCertificate != null) {
                $uploaderHelper->deleteСertificate($userCertificate);
            }
        }

    }

}
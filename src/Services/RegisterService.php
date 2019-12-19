<?php


namespace App\Services;

use App\Entity\User;
use App\Entity\UserCertificate;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;
use Doctrine\ORM\EntityManagerInterface;


class RegisterService
{
    const VERIFY_URL = "127.0.0.1:8080/register/verify";

    private $mailer,
        $templating,
        $entityManager,
        $router;

    public function __construct(EntityManagerInterface $em, Swift_Mailer $mailer,
                                Environment $templating, RouterInterface $router)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->entityManager = $em;
        $this->router = $router;
    }

    public function makeExpertValid($expertId): bool
    {
        if ($expertId) {
            $user = $this->entityManager
                ->getRepository(User::class)
                ->find($expertId);

            if ($user == null) {
                return false;
            }

            $user->setRoles([User::ROLE_EXPERT]);
            try{
                $this->entityManager->flush();
                return true;
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    public function registerUser(
        Form $userForm,
        User $user,
        UserPasswordEncoderInterface $passwordEncoder,
        UploaderHelper $uploaderHelper
    ): ?User
    {
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user->setRoles([User::ROLE_USER]);
            $this->saveForm($userForm, $user, $passwordEncoder, $uploaderHelper);
            $this->emailVerification($user);
            return $user;
        }
        return null;
    }

    public function saveForm(
        Form $form,
        User $user,
        UserPasswordEncoderInterface $passwordEncoder,
        UploaderHelper $uploaderHelper
    ): void
    {
        $random = (string)uniqid();
        $user->setVerifyCode($random);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            )
        );

        $newFilename = "";
        if (array_key_exists('certificate', $form->all())) {
            if ($form['certificate']->getData() !== null) {
                $certificate = $form['certificate']->getData();
                $newFilename = $uploaderHelper->uploadFile($certificate);
            }
        }

        $this->entityManager->persist($user);
        try {
            $this->entityManager->flush();
            if ($newFilename != '' && in_array(User::ROLE_USER, $user->getRoles())) {
                $userCertificate = new UserCertificate();
                $userCertificate->setCertificate('certificate/' . $newFilename);
                $userCertificate->setUser($user);
                $this->entityManager->persist($userCertificate);
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            $uploaderHelper->deleteСertificate($certificate);
        }
    }

    public function emailVerification(User $user): void
    {
        $email = $user->getEmail();
        $name = $user->getName();
        $message = (new Swift_Message('Checking of e-mail sending'))
            ->setFrom('beauty-routine@yandex.ru')
            ->setTo($email)
            ->setBody(
                $this->templating->render(
                    'email/verificationEmail.html.twig',
                    ['name' => $name,
                    'verifyCode' => $user->getVerifyCode(),
                    'verifyUrl' => self::VERIFY_URL, ]
                ),
                'text/html');
        $this->mailer->send($message);
    }

    public function verifyUser(string $verifyCode): bool
    {
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['verifyCode' => $verifyCode]);

        /**@var User $user */
        if($user){
            $user->setVerifyCode(null);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } else{
            throw new \Exception("Invalid verification code.");
        }
        return true;
    }
}

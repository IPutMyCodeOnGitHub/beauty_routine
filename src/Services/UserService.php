<?php


namespace App\Services;

use App\Entity\User;
use App\Entity\UserCertificate;
use Doctrine\Common\Persistence\ObjectManager;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as AC;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

/**
 * @Route("")
 */
class UserService
{
    private $manager;
    private $mailer;
    private $templating;

    public function __construct(ObjectManager $manager, Swift_Mailer $mailer, Environment $templating)
    {
        $this->manager = $manager;
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function makeExpertValid($expertId, $entityManager): void
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
            $user->setRoles([User::ROLE_EXPERT]);
            $entityManager->flush();
        }
    }

    public function saveForm(Form $form, User $user, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper): void
    {
        $random = (string)rand(10000, 99999);
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
                $newFilename = $uploaderHelper->uploadCertificatePDF($certificate);
            }
        }

        $this->manager->persist($user);
        try {
            $this->manager->flush();
            if ($newFilename != '' && in_array(User::ROLE_INVALID_EXPERT, $user->getRoles())) {
                $userCertificate = new UserCertificate();
                $userCertificate->setCertificate('certificate/' . $newFilename);
                $userCertificate->setUser($user);
                $this->manager->persist($userCertificate);
                $this->manager->flush();
            }
        } catch (\Exception $e) {
            $uploaderHelper->deleteСertificate($certificate);
        }
    }

    public function emailVerification(User $user)
    {
        $email = $user->getEmail();
        $name = $user->getName();
        $verifyUrl = "127.0.0.1:8080/register/verify";
        $message = (new Swift_Message('Проверка отправки письма'))
            ->setFrom('beauty-routine@yandex.ru')
            ->setTo($email)
            ->setBody(
                $this->templating->render(
                    'email/verificationEmail.html.twig',
                    ['name' => $name,

                    'verifyCode' => $user->getVerifyCode(),
                    'verifyUrl' =>$verifyUrl, ]

                ),
                'text/html');
        $this->mailer->send($message);
    }
}

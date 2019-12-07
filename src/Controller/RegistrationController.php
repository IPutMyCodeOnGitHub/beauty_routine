<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationUserFormType;
use App\Form\RegistrationExpertFormType;
use App\Repository\UserRepository;
use App\Services\UploaderHelper;
use App\Services\UserService;
use Doctrine\ORM\EntityManager;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/register")
 */
class RegistrationController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper): Response
    {
        $user = new User();

        $expertForm = $this->createForm(RegistrationExpertFormType::class, $user);
        $expertForm->handleRequest($request);
        if ($expertForm->isSubmitted() && $expertForm->isValid()) {
            $user->setRoles([User::ROLE_INVALID_EXPERT]);
            $this->userService->saveForm($expertForm, $user, $passwordEncoder, $uploaderHelper);
            $this->userService->emailVerification($user);
            return $this->redirectToRoute('app_login');
        }

        $userForm = $this->createForm(RegistrationUserFormType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user->setRoles([User::ROLE_USER]);
            $this->userService->saveForm($userForm, $user, $passwordEncoder, $uploaderHelper);
            $this->userService->emailVerification($user);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationUserForm' => $userForm->createView(),
            'registrationExpertForm' => $expertForm->createView(),
        ]);
    }
    /**
     * @Route("/verify/{verifyCode}", name="verification")
     */
    public function userVerification(Request $request, string $verifyCode)
    {
        $entityManager = $this->getDoctrine()
            ->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['verifyCode' => $verifyCode]);

        /**@var User $user */
        if($user){
            $user->setVerifyCode(null);
            $entityManager->persist($user);
            $entityManager->flush();
        } else{
            throw new \Exception("Invalid verification code.");
        }
        return $this->render('profile-user/verificationPage.html.twig', [
            'message' => "Вы успешно прошли регестрацию!",
        ]);
    }
}

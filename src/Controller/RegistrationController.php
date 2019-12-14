<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationUserFormType;
use App\Form\RegistrationExpertFormType;
use App\Services\UploaderHelper;
use App\Services\RegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegistrationController extends AbstractController
{
    private $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    /**
     * @Route("/register", name="app.register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper): Response
    {
        $user = new User();

        $expertForm = $this->createForm(RegistrationExpertFormType::class, $user);
        $expertForm->handleRequest($request);

        $userForm = $this->createForm(RegistrationUserFormType::class, $user);
        $userForm->handleRequest($request);

        $registeredUser = $this->registerService->registerUser($userForm, $user, $passwordEncoder, $uploaderHelper);

        if ($registeredUser) {
            return $this->redirectToRoute('app.login');
        }

        $registeredExpert = $this->registerService->registerUser($expertForm, $user, $passwordEncoder, $uploaderHelper);

        if ($registeredExpert) {
            return $this->redirectToRoute('app.expert.login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationUserForm' => $userForm->createView(),
            'registrationExpertForm' => $expertForm->createView(),
        ]);
    }
    /**
     * @Route("/verify/{verifyCode}", name="verification")
     */
    public function userVerification(Request $request, string $verifyCode): Response
    {
        $redirectRoute = $this->registerService->verifyUser($verifyCode);

        return $this->redirectToRoute('app.login');
    }
}

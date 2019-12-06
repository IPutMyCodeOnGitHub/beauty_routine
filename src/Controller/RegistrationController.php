<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationUserFormType;
use App\Form\RegistrationExpertFormType;
use App\Repository\UserRepository;
use App\Services\UploaderHelper;
use App\Services\UserService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UploaderHelper $uploaderHelper): Response
    {
        $user = new User();

        $expertForm = $this->createForm(RegistrationExpertFormType::class, $user);
        $expertForm->handleRequest($request);
        if ($expertForm->isSubmitted() && $expertForm->isValid()) {
            $user->setRoles([User::ROLE_INVALID_EXPERT]);
            $this->userService->saveForm($expertForm, $user, $passwordEncoder, $uploaderHelper);
            return $this->redirectToRoute('app_login');
        }

        $userForm = $this->createForm(RegistrationUserFormType::class, $user);
        $userForm->handleRequest($request);
        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user->setRoles([User::ROLE_USER]);
            $this->userService->saveForm($userForm, $user, $passwordEncoder, $uploaderHelper);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationUserForm' => $userForm->createView(),
            'registrationExpertForm' => $expertForm->createView(),
        ]);
    }

}

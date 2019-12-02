<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationUserFormType;
use App\Form\RegistrationExpertFormType;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserService $userService): Response
    {
        $user = new User();

        $userForm = $this->createForm(RegistrationUserFormType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user->addRole("ROLE_USER");
            $userService->saveForm($userForm);
            return $this->redirectToRoute('app_login');
        }

        $expertForm = $this->createForm(RegistrationExpertFormType::class, $user);
        $expertForm->handleRequest($request);
        if ($expertForm->isSubmitted() && $expertForm->isValid()) {
            $user->addRole("ROLE_EXPERT");
            $userService->saveForm($expertForm);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationUserForm' => $userForm->createView(),
            'registrationExpertForm' => $expertForm->createView(),
        ]);
    }

}

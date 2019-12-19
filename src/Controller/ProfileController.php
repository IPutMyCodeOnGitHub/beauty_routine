<?php

namespace App\Controller;

use App\Entity\RoutineSelection;
use App\Entity\User;
use App\Form\ExpertProfileFormType;
use App\Form\RegistrationExpertFormType;
use App\Services\RoutineService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private $userService,
            $routineService;

    public function __construct(
        UserService $userService,
        RoutineService $routineService)
    {
        $this->userService = $userService;
        $this->routineService = $routineService;
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function showProfile(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $entityManager = $this->getDoctrine()->getManager();
        $routineSelections = $entityManager->getRepository(RoutineSelection::class)
            ->userRoutineSelection(null, null, $user);

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'routines' => $routineSelections
        ]);
    }

    /**
     * @Route("/profile/expert", name="profile.expert")
     */
    public function showExpertProfile(Request $request): Response
    {
        /** @var User $expert */
        $expert = $this->getUser();

        $page = $request->query->getInt('page', 1);
        $routines = $this->routineService->searchRoutine($expert->getName(), null, $page, null, null);

        return $this->render('profile-expert/profile-expert.html.twig', [
            'expert' => $expert,
            'routines' => $routines,
        ]);
    }

    /**
     * @Route("/profile/expert/edit", name="profile.expert.edit")
     */
    public function profileExpertEdit(Request $request): Response
    {
        /** @var User $expert */
        $expert = $this->getUser();

        $form = $this->createForm(ExpertProfileFormType::class, $expert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->userService->editExpertProfile($form, $expert, $request);
            if ($result) {
                $this->addFlash('success', 'Profile updated!');
                return $this->redirectToRoute('profile.expert');
            } else {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }
        return $this->render('profile-expert/profile-expert-edit.html.twig', [
            'form' => $form->createView(),
            'expert' => $expert,
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\RoutineSelection;
use App\Entity\RoutineType;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function showProfile()
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
    public function showExpertProfile()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profile-expert/profile-expert.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/profile/expert/edit", name="profile.expert.edit")
     */
    public function profileExpertEdit()
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profile-expert/profile-expert-edit.html.twig', [
            'user' => $user,
        ]);
    }
}

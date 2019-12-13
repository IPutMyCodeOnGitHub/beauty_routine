<?php

namespace App\Controller;

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
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController'
            ]);
    }

    /**
     * @Route("/profile/expert", name="profile.expert")
     */
    public function showExpertProfile()
    {
        /** @var User $user */
        $user = $this->getUser();
        $certificates = $user->getUserCertificates();
        return $this->render('profile-expert/profile-expert.html.twig', [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'id' => $user->getId(),
            //'certificatesPaths' => $certificates,
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
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }
}

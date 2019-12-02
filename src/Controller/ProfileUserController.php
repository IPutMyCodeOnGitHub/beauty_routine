<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileUserController extends AbstractController
{
    /**
     * @Route("/profile-user", name="profile")
     */
    public function index()
    {
        return $this->render('profile-user/index.html.twig', [
            'controller_name' => 'ProfileUserController',
        ]);
    }
}

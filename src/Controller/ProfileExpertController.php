<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ProfileExpertController extends AbstractController
{
    /**
     * @Route("/profile-expert", name="profile")
     */
    public function index()
    {
        return $this->render('profile-expert/index.html.twig', [
            'controller_name' => 'ProfileExpertController',
        ]);
    }
}

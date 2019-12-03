<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile-expert", name="profile.")
 */
class ProfileExpertController extends AbstractController
{
    /**
     * @Route("/", name="expert")
     */
    public function index()
    {
        return $this->render('profile-expert/index.html.twig', [
            'controller_name' => 'ProfileExpertController',
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin.")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/manage-user", name="users")
     */
    public function manageUser()
    {
        //ToDo: Block can be used for present a statistic in content area

        return $this->render('admin/manage-users.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/manage-user/experts", name="experts")
     */
    public function manageExperts()
    {
        return $this->render('admin/manage-experts.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}

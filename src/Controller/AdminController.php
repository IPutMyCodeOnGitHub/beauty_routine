<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\PaginatorService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function manageUser():Response
    {
        //ToDo: Block can be used for present a statistic in content area

        return $this->render('admin/manage-users.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}

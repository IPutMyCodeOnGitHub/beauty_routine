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

    /**
     * @Route("/manage-user/experts", name="experts", methods={"GET"})
     */
    public function manageExperts(Request $request, UserService $userService, PaginatorService $paginatorService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        //Todo: make it with AJAX
        $expertId = $request->query->get('id');
        $userService->makeExpertValid($expertId, $entityManager);
        //

        $page = $request->query->getInt('page', 1);

        $experts = $paginatorService->findUserByRolePaginator($entityManager, $page);

        return $this->render('admin/manage-experts.html.twig', [
            'controller_name' => 'AdminController',
            'experts' => $experts,
        ]);
    }
}

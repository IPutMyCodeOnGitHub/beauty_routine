<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Services\AdminService;
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
     * @Route("/", name="stats", methods={"GET"})
     */
    public function userStatistic(AdminService $adminService):Response
    {
        $usersCount = count($adminService->allUsers());
        $adminCount = $adminService->getAdmins(null)->count();
        $guests = $adminService->searchUser(null, null)->count();
        $experts = $adminService->searchExpert(null, null)->count();

        return $this->render('admin/manage-users/manage-users.html.twig',
            [
                'users' => $usersCount,
                'admins' => $adminCount,
                'guests' => $guests,
                'experts' => $experts,
            ]);
    }

    /**
     * @Route("/admins", name="admins", methods={"GET"})
     */
    public function manageAdmins(Request $request, AdminService $adminService): Response
    {
        $search = $request->query->get('search');
        $page = $request->query->getInt('page', 1);

        if ($search) {
            $page = 1;
            $request->query->remove('page');
        }

        $admins = $adminService->getAdmins($search, $page);

        return $this->render('admin/manage-users/admin/manage-admin.html.twig', [
            'admins' => $admins,
        ]);
    }
}

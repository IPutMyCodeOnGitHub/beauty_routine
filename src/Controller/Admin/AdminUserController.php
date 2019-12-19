<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Services\AdminService;
use App\Services\RegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin.")
 */
class AdminUserController extends AbstractController
{
    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function manageUsers(Request $request, AdminService $adminService): Response
    {
        $search = $request->query->get('search');
        $valid = $request->query->get('valid');
        $page = $request->query->getInt('page', 1);

        if ($search || $valid) {
            $page = 1;
            $request->query->remove('page');
        }

        $users = $adminService->searchUser($search, $valid, $page);

        return $this->render('admin/manage-users/user/manage-users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/users/{id}/delete", name="users.delete", methods={"POST"})
     */
    public function ajaxUserDelete(int $id, Request $request, AdminService $adminService): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(false);
        }

        $result = $adminService->ajaxUserDelete($id);

        return new Response($result);
    }
}
<?php

namespace App\Controller\Admin;

use App\Services\AdminService;
use App\Services\RegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin.")
 */
class AdminExpertController extends AbstractController
{
    /**
     * @Route("/experts/{id}/validation", name="experts.validation", methods={"POST"})
     */
    public function ajaxExpertValidation(int $id, Request $request, RegisterService $userService, AdminService $adminService): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(false);
        }

        $user = $adminService->searchUserById($id);

        if ($user) {
            $result = $userService->makeExpertValid($user);
            return new Response($result);
        }

        return new Response(false);
    }

    /**
     * @Route("/experts/{id}/delete", name="experts.delete", methods={"POST"})
     */
    public function ajaxExpertDelete(int $id, Request $request, AdminService $adminService): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(false);
        }

        $result = $adminService->ajaxExpertDelete($id);

        return new Response($result);
    }

    /**
     * @Route("/experts", name="experts", methods={"GET"})
     */
    public function manageExperts(Request $request, AdminService $adminService): Response
    {
        $search = $request->query->get('search');
        $active = $request->query->get('active');
        $page = $request->query->getInt('page', 1);

        if ($search || $active) {
            $page = 1;
            $request->query->remove('page');
        }

        $experts = $adminService->searchExpert($search, $active, $page);

        return $this->render('admin/manage-users/experts/manage-experts.html.twig', [
            'experts' => $experts,
        ]);
    }
}
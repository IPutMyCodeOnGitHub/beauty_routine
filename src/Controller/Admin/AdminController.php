<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Services\RegisterService;
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
     * @Route("/user-stats", name="stats", methods={"GET"})
     */
    public function userStatistic():Response
    {
        //ToDo: Block can be used for present a statistic in content area

        return $this->render('admin/manage-users/manage-users.html.twig');
    }

    /**
     * @Route("/experts/{id}/validation", name="experts.validation", methods={"POST"})
     */
    public function ajaxExpertValidation(int $id, Request $request, RegisterService $userService): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response('0');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager
            ->getRepository(User::class)
            ->find($id);

        $result = $userService->makeExpertValid($user);

        return new Response($result);
    }

    /**
     * @Route("/experts", name="experts", methods={"GET"})
     */
    public function manageExperts(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $search = $request->query->get('search');
        $active = $request->query->get('active');
        $page = $request->query->getInt('page', 1);

        if ($search || $active) {
            $page = 1;
            $request->query->remove('page');
        }

        $experts = $entityManager
            ->getRepository(User::class)
            ->findSearchExpertPaginator($search, $active, $page);

        return $this->render('admin/manage-users/experts/manage-experts.html.twig', [
            'experts' => $experts,
        ]);
    }

    /**
     * @Route("/users", name="users", methods={"GET"})
     */
    public function manageUsers(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $search = $request->query->get('search');
        $valid = $request->query->get('valid');
        $page = $request->query->getInt('page', 1);

        if ($search || $valid) {
            $page = 1;
            $request->query->remove('page');
        }

        $users = $entityManager
            ->getRepository(User::class)
            ->findSearchUserPaginator($search, $valid, $page);

        return $this->render('admin/manage-users/user/manage-users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/admins", name="admins", methods={"GET"})
     */
    public function manageAdmins(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $search = $request->query->get('search');
        $page = $request->query->getInt('page', 1);

        if ($search) {
            $page = 1;
            $request->query->remove('page');
        }

        $admins = $entityManager
            ->getRepository(User::class)
            ->findSearchAdminsPaginator($search, $page);

        return $this->render('admin/manage-users/admin/manage-admin.html.twig', [
            'admins' => $admins,
        ]);
    }


}

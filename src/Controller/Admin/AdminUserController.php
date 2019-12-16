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
class AdminUserController extends AbstractController
{
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
     * @Route("/users/{id}/delete", name="users.delete", methods={"POST"})
     */
    public function ajaxUserDelete(int $id, Request $request, RegisterService $userService): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response('0');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            return new Response(0);
        }

        $entityManager->remove($user);
        $entityManager->flush();

        return new Response(1);
    }
}
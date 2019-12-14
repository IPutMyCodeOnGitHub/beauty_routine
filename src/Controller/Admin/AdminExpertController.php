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
class AdminExpertController extends AbstractController
{
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
}
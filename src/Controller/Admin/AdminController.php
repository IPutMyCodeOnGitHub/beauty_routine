<?php

namespace App\Controller\Admin;

use App\Entity\RoutineType;
use App\Entity\User;
use App\Form\TypeOfRoutineType;
use App\Services\RegisterService;
use App\Services\RoutineTypeService;
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

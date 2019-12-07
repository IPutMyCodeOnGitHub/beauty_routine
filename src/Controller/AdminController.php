<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use \Knp\Component\Pager\Paginator;
use App\Services\UserService;
use Knp\Component\Pager\PaginatorInterface;
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
     * @Route("/manage-user", name="users", methods={"GET"})
     */
    public function manageUser():Response
    {
        //ToDo: Block can be used for present a statistic in content area

        return $this->render('admin/manage-users/manage-users.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/manage-user/experts/validation", name="experts.validation", methods={"POST"})
     */
    public function ajaxExpertValidation(Request $request, UserService $userService): Response
    {
        if (!$request->isXmlHttpRequest()) {
            throw new \Exception;
        }
        $entityManager = $this->getDoctrine()->getManager();

        $expertId = $request->request->get('id');
        $userService->makeExpertValid($expertId, $entityManager);

        return new Response($expertId);
    }

    /**
     * @Route("/manage-user/experts", name="manage.experts", methods={"GET"})
     */
    public function manageExperts(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $findByNameExpert = $request->query->get('findByNameExpert');
        $findByEmailExpert = $request->query->get('findByEmailExpert');
        $checkActiveExpert = $request->query->get('checkActiveExpert');
        $page = $request->query->getInt('page', 1);

        if ($findByNameExpert || $findByEmailExpert || $checkActiveExpert) {
            $page = 1;
            $request->query->remove('page');
        }

        $experts = $entityManager
            ->getRepository(User::class)
            ->findSearchExpertPaginator($paginator, $findByNameExpert, $findByEmailExpert, $checkActiveExpert, $page);

        return $this->render('admin/manage-users/experts/manage-experts.html.twig', [
            'controller_name' => 'AdminController',
            'experts' => $experts,
        ]);
    }

    /**
     * @Route("/manage-user/users", name="manage.users", methods={"GET"})
     */
    public function manageUsers(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $findByNameUser = $request->query->get('findByNameUser');
        $findByEmailUser = $request->query->get('findByEmailUser');
        $checkValidUser = $request->query->get('checkValidUser');
        $page = $request->query->getInt('page', 1);

        if ($findByNameUser || $findByEmailUser || $checkValidUser) {
            $page = 1;
            $request->query->remove('page');
        }

        $users = $entityManager
            ->getRepository(User::class)
            ->findSearchUserPaginator($paginator, $findByNameUser, $findByEmailUser, $checkValidUser, $page);

        return $this->render('admin/manage-users/user/manage-users.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users,
        ]);
    }

    /**
     * @Route("/manage-user/admins", name="manage.admins", methods={"GET"})
     */
    public function manageAdmins(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $findByNameAdmin = $request->query->get('findByNameAdmin');
        $findByEmailAdmin = $request->query->get('findByEmailAdmin');
        $page = $request->query->getInt('page', 1);

        if ($findByNameAdmin || $findByEmailAdmin) {
            $page = 1;
            $request->query->remove('page');
        }

        $admins = $entityManager
            ->getRepository(User::class)
            ->findSearchAdminsPaginator($paginator, $findByNameAdmin, $findByEmailAdmin, $page);

        return $this->render('admin/manage-users/admin/manage-admin.html.twig', [
            'controller_name' => 'AdminController',
            'admins' => $admins,
        ]);
    }


}

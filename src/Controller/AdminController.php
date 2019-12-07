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
     * @Route("/manage-user/experts", name="experts", methods={"GET"})
     */
    public function manageExperts(Request $request, PaginatorInterface $paginator): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $findByNameExpert = $request->query->get('findByNameExpert');
        $findByEmailExpert = $request->query->get('findByEmailExpert');
        $checkActiveExpert = $request->query->get('checkActiveExpert');
        $page = $request->query->getInt('page', 1);
//dd($request->query->all(), $findByNameExpert, $findByEmailExpert, $checkActiveExpert, $page);
        $experts = $entityManager
            ->getRepository(User::class)
            ->findSearchExpertPaginator($paginator, $findByNameExpert, $findByEmailExpert, $checkActiveExpert, $page);

        return $this->render('admin/manage-experts.html.twig', [
            'controller_name' => 'AdminController',
            'experts' => $experts,
        ]);
    }


}

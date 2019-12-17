<?php

namespace App\Controller\Admin;

use App\Entity\Routine;
use App\Entity\RoutineType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin.")
 */
class AdminRoutineController extends AbstractController
{
    /**
     * @Route("/routine", name="routine", methods={"GET", "POST"})
     */
    public function routineList(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $expert = $request->query->get('expert');
        $type = $request->query->get('type');
        $page = $request->query->getInt('page', 1);

        if ($expert || $type) {
            $page = 1;
            $request->query->remove('page');
        }

        if ($type) {
            $type = $entityManager->getRepository(RoutineType::class)->find($type);
        }

        $routines = $entityManager
            ->getRepository(Routine::class)
            ->searchRoutinePaginator($expert, $type, $page, null, null);

        $types = $entityManager
            ->getRepository(RoutineType::class)
            ->findAll();

        return $this->render('admin/routine/list.html.twig', [
            'types' => $types,
            'routines' => $routines,
        ]);
    }

    /**
     * @Route("/routine/{id}/edit", name="routine.edit", methods={"GET", "POST"})
     */
    public function routineEdit(int $id): Response
    {
        return $this->render('admin/routine/edit.html.twig', [
//            'types' => $types,
        ]);
    }

    /**
     * @Route("/routine/{id}/delete", name="routine.delete", methods={"GET",  "POST"})
     */
    public function routineDelete(int $id): Response
    {
        return new Response(0);
    }


}
<?php

namespace App\Controller\Admin;

use App\Entity\RoutineType;
use App\Form\TypeOfRoutineType;
use App\Services\AdminService;
use App\Services\RoutineTypeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin.")
 */
class AdminRoutineTypeController extends AbstractController
{
    /**
     * @Route("/type", name="type", methods={"GET"})
     */
    public function routineTypes(AdminService $adminService): Response
    {
        $types = $adminService->routineTypes();

        return $this->render('admin/routine.type/list.html.twig', [
            'types' => $types,
        ]);
    }

    /**
     * @Route("/type/create", name="type.create", methods={"GET", "POST"})
     */
    public function routineTypesCreate(Request $request, AdminService $adminService): Response
    {
        $type = new RoutineType();

        $form = $this->createForm(TypeOfRoutineType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $adminService->routineTypesCreate($type);
            $this->addFlash($result['type'], $result['message']);
            return $this->redirectToRoute('admin.type');
        }

        return $this->render('admin/routine.type/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/type/{id}/delete", name="type.delete", methods={"GET", "POST"})
     */
    public function routineTypesAjaxDelete(int $id, AdminService $adminService): Response
    {
        $result = $adminService->routineTypesAjaxDelete($id);

        return new Response($result);
    }

    /**
     * @Route("/type/{id}/edit", name="type.edit", methods={"GET", "POST"})
     */
    public function routineTypesEdit(int $id, Request $request, RoutineTypeService $routineTypeService, AdminService $adminService): Response
    {
        $type = $routineTypeService->getType($id);

        if (!$type) {
            $this->addFlash('danger', 'Type was not found.');
            return $this->redirectToRoute('admin.type');
        }

        $form = $this->createForm(TypeOfRoutineType::class, $type);

        $result = $adminService->routineTypesEdit($form, $type, $request);

        if ($result) {
            $this->addFlash($result['type'], $result['message']);
            return $this->redirectToRoute($result['route']);
        }

        return $this->render('admin/routine.type/edit.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
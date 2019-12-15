<?php

namespace App\Controller\Admin;

use App\Entity\RoutineType;
use App\Form\TypeOfRoutineType;
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
    public function routineTypes(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $types = $entityManager
            ->getRepository(RoutineType::class)
            ->getAllTypes();

        return $this->render('admin/routine.type/list.html.twig', [
            'types' => $types,
        ]);
    }

    /**
     * @Route("/type/create", name="type.create", methods={"GET", "POST"})
     */
    public function routineTypesCreate(Request $request): Response
    {
        $type = new RoutineType();

        $form = $this->createForm(TypeOfRoutineType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($type);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'New type added');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error: ' . $e);
            }
        }
        return $this->render('admin/routine.type/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/type/{id}/delete", name="type.delete", methods={"GET", "POST"})
     */
    public function routineTypesAjaxDelete(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $type = $entityManager->getRepository(RoutineType::class)->find($id);

        if (!$type) {
            return new Response(0);
        }

        $entityManager->remove($type);
        $entityManager->flush();

        return new Response(1);
    }

    /**
     * @Route("/type/{id}/edit", name="type.edit", methods={"GET", "POST"})
     */
    public function routineTypesEdit(int $id, Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $type = $entityManager->getRepository(RoutineType::class)->find($id);

        if (!$type) {
            $this->addFlash('danger', 'Type was not found.');
            return $this->redirectToRoute('admin.type');
        }

        $form = $this->createForm(TypeOfRoutineType::class, $type);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($type);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Type updated');
                return $this->redirectToRoute('admin.type');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Error: ' . $e);
                return $this->redirectToRoute('admin.type');
            }
        }
        return $this->render('admin/routine.type/edit.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
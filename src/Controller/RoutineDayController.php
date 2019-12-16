<?php

namespace App\Controller;

use App\Entity\Routine;
use App\Entity\RoutineDay;
use App\Entity\RoutineSelection;
use App\Entity\RoutineType;
use App\Entity\User;
use App\Form\RoutineDayType;
use App\Form\RoutineFormType;
use App\Services\RoutineService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class RoutineDayController extends AbstractController
{
    /**
     * @Route("/expert/routine/{id}/day/create", name="expert.day.routine.create")
     */
    public function createDay(int $id, Request $request, RoutineService $routineService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $routine = $entityManager->getRepository(Routine::class)->find($id);

        if (!$routine) {
            throw $this->createNotFoundException('Routine does not exist');
        }

        $routineDay = new RoutineDay();
        $form = $this->createForm(RoutineDayType::class, $routineDay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $routineService->createDay($routineDay, $routine);
            if ($result) {
                $this->addFlash('success', 'Day added!');
                return $this->redirectToRoute('expert.routine.day.edit', ['id' => $id, 'dayId' => $routineDay->getId()]);
            } else {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }
        return $this->render('routine/day.create.html.twig', [
            'routine' => $routine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/expert/routine/{id}/day/{dayId}/edit", name="expert.routine.day.edit")
     */
    public function editDay(Request $request, int $id, int $dayId, RoutineService $routineService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $routineDay = $entityManager->getRepository(RoutineDay::class)->find($dayId);

        if (!$routineDay) {
            $this->addFlash('danger', 'Sorry, day does not exist.');
            return $this->redirectToRoute('routine.edit', ['id' => $id, 'dayId' => $dayId]);
        }

        $form = $this->createForm(RoutineDayType::class, $routineDay);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $result =$routineService->editDay($routineDay);
            if ($result) {
                $this->addFlash('success', 'Day updated!');
                return $this->redirectToRoute('expert.routine.edit', ['id' => $id, 'dayId' => $dayId]);
            } else {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }

        return $this->render('routine/day.edit.html.twig', [
            'form' => $form->createView(),
            'day' => $routineDay
        ]);
    }

    /**
     * @Route("/expert/routine/{id}/day/{dayId}/delete", name="expert.routine.day.delete")
     */
    public function deleteDay(Request $request, int $id, int $dayId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $routineDay = $entityManager
            ->getRepository(RoutineDay::class)
            ->find($dayId);
        if (!$routineDay) {
            $this->addFlash('danger', 'Sorry, day doesn\'t exists.');
            return new Response(0);
        }
        $entityManager->remove($routineDay);
        $entityManager->flush();
        return new Response(1);
    }

    /**
     * @Route("/expert/routine/{id}/day/{dayId}/addproduct", name="expert.routine.day.add.product")
     */
    public function addProductInDay(int $id, int $dayId): Response
    {
        return new Response(0);
    }
}
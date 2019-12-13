<?php

namespace App\Controller;

use App\Entity\Routine;
use App\Entity\RoutineDay;
use App\Form\RoutineDayType;
use App\Form\RoutineFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/expert/routine")
 */
class RoutineController extends AbstractController
{
    /**
     * @Route("/", name="routine")
     */
    public function listRoutines(): Response
    {
        //ToDo: search
        /**
         * @var User $expert
         */
        $expert = $this->getUser();

        if (!$expert) {
            throw $this->createNotFoundException('The expert does not exist');
        }

        $routines = $expert->getRoutines();

        return $this->render('routine/list.html.twig', [
            'routines' => $routines,
        ]);
    }

    /**
     * @Route("/create", name="routine.create")
     */
    public function create(Request $request): Response
    {
        $routine = new Routine();
        $form = $this->createForm(RoutineFormType::class, $routine);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $routine->setStatus(Routine::STATUS_DRAFT);
            $routine->setUser($this->getUser());

            $entityManager->persist($routine);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Routine added!');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }

        return $this->render('routine/create.html.twig', [
            'title' => 'Создание программы',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="routine.edit")
     */
    public function edit(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        /**
         * @var Routine $routine
         */
        $routine = $entityManager->getRepository(Routine::class)->find($id);

        if (!$routine) {
            throw $this->createNotFoundException('The routine does not exist');
        }

        $form = $this->createForm(RoutineFormType::class, $routine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($routine);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Routine updated!');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }

        $routineDay = new RoutineDay();
        $formDay = $this->createForm(RoutineDayType::class, $routineDay);
        $formDay->handleRequest($request);

        if ($formDay->isSubmitted() && $formDay->isValid()) {
            $routineDay->setRoutine($routine);
            $entityManager->persist($routineDay);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Day added!');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }

        return $this->render('routine/edit.html.twig', [
            'form' => $form->createView(),
            'formDay' => $formDay->createView(),
            'days' => $routine->getRoutineDays(),
        ]);
    }

    /**
     * @Route("/edit/{id}/day/{dayId}", name="routine.day.edit")
     */
    public function editDay(Request $request, int $id, int $dayId): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $routineDay = $entityManager->getRepository(RoutineDay::class)->find($dayId);

        if (!$routineDay) {
            $this->addFlash('danger', 'Sorry, day does not exist.');
            return $this->redirectToRoute('routine.edit', ['id' => $id, 'dayId' => $dayId]);
        }

        $formDay = $this->createForm(RoutineDayType::class, $routineDay);
        $formDay->handleRequest($request);
        if ($formDay->isSubmitted() && $formDay->isValid()) {
            $entityManager->persist($routineDay);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Day updated!');
                return $this->redirectToRoute('routine.edit', ['id' => $id, 'dayId' => $dayId]);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }

        return $this->render('routine/day.edit.html.twig', [
            'form' => $formDay->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}/day/{dayId}/delete", name="routine.day.delete")
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

}

<?php

namespace App\Controller\Routine;

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
class RoutineController extends AbstractController
{
    /**
     * @Route("/expert/routine", name="expert.routine")
     */
    public function listRoutines(Request $request, RoutineService $routineService): Response
    {
        $expert = $this->getUser();
        if (!$expert) {
            throw $this->createNotFoundException('The expert does not exist');
        }

        $status = $request->query->get('status');
        $type = $request->query->get('type');
        $page = $request->query->getInt('page', 1);

        if ($type || $status) {
            $page = 1;
            $request->query->remove('page');
        }

        if ($type || isset($type)) {
            $type = $routineService->getTypeById($type);
        }

        $routines = $routineService->searchRoutine($expert->getName(), $type, $page, null, $status);

        $types = $routineService->getRoutineTypes();

        return $this->render('routine/list.html.twig', [
            'routines' => $routines,
            'types' => $types,
        ]);
    }

    /**
     * @Route("/expert/routine/create", name="expert.routine.create")
     */
    public function create(Request $request, RoutineService $routineService): Response
    {
        $routine = new Routine();

        $form = $this->createForm(RoutineFormType::class, $routine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $result = $routineService->createRoutineForm($this->getUser(), $form, $routine);
            if ($result) {
                $this->addFlash('success', 'Routine added!');
                return $this->redirectToRoute("expert.routine");
            } else {
                $this->addFlash('danger', 'Error. Routine was not added.');
            }
        }

        return $this->render('routine/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/expert/routine/{id}/delete", name="expert.routine.delete")
     */
    public function delete(int $id, RoutineService $routineService): Response
    {
        $routine = $routineService->getRoutineById($id);

        if (!$routine) {
            return new Response(false);
        }

        $result = $routineService->deleteRoutine($routine);
        return new Response($result);
    }

    /**
     * @Route("/expert/routine/{id}/edit", name="expert.routine.edit")
     */
    public function edit(Request $request, int $id, RoutineService $routineService): Response
    {
        /**
         * @var Routine $routine
         */
        $routine = $routineService->getRoutineById($id);

        if (!$routine) {
            throw $this->createNotFoundException('The routine does not exist');
        }

        $form = $this->createForm(RoutineFormType::class, $routine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $routineService->editRoutine($form, $routine);
            if ($result) {
                $this->addFlash('success', 'Routine updated!');
                return $this->redirectToRoute("expert.routine");
            } else {
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }

        return $this->render('routine/edit.html.twig', [
            'form' => $form->createView(),
            'routine' => $routine,
        ]);
    }

    /**
     * @Route("/expert/routine/{id}/activate", name="expert.routine.activate")
     */
    public function activateRoutineAjax(int $id, RoutineService $routineService): Response
    {
        $routine = $routineService->getRoutineById($id);

        if (!$routine) {
            return new Response(false);
        }

        $result = $routineService->activateRoutine($routine);

        return new Response($result);
    }

    /**
     * @Route("/expert/routine/{id}/deactivate", name="expert.routine.deactivate")
     */
    public function deactivateRoutineAjax(int $id, RoutineService $routineService): Response
    {
        $routine = $routineService->getRoutineById($id);

        if (!$routine) {
            return new Response(false);
        }

        $result = deactivateRoutine($routine);

        return new Response($result);
    }


    /**
     * @Route("/routine/", name="user.routine")
     */
    public function userListRoutine(Request $request, RoutineService $routineService): Response
    {

        $expert = $request->query->get('expert');
        $type = $request->query->get('type');
        $page = $request->query->getInt('page', 1);

        if ($expert || $type) {
            $page = 1;
            $request->query->remove('page');
        }

        if ($type) {
            $type = $routineService->getTypeById($type);
        }

        $user = $this->getUser();

        $routines = $routineService->searchRoutine($expert, $type, $page, null, Routine::STATUS_ACTIVE);

        $types = $routineService->getRoutineTypes();

        return $this->render('routine/user.list.html.twig', [
            'routines' => $routines,
            'user' => $user,
            'types' => $types,
        ]);
    }

    /**
     * @Route("/routine/show/{id}", name="user.routine.show")
     */
    public function userRoutineShow(Request $request, int $id, RoutineService $routineService): Response
    {
        $routine = $routineService->getRoutineById($id);

        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('The user does not exist');
        }

        return $this->render('routine/user.routine.show.html.twig', [
            'routine' => $routine,
            'user' => $user
        ]);
    }
}

<?php

namespace App\Controller\Routine;

use App\Entity\Routine;
use App\Entity\RoutineSelection;
use App\Entity\RoutineType;
use App\Entity\RoutineUserDay;
use App\Services\RoutineSelectionService;
use App\Services\RoutineService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class RoutineSelectionController extends AbstractController
{
    /**
     * @Route("/routine/sub", name="user.sub.routine.show")
     */
    public function userSubListRoutine(Request $request, RoutineService $routineService, RoutineSelectionService $routineSelectionService): Response
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
        $routines = $routineSelectionService->searchRoutine($expert, $type, $user, $page);

        $types = $routineService->getRoutineTypes();

        return $this->render('routine/user.sub.list.html.twig', [
            'routines' => $routines,
            'types' => $types,
        ]);
    }

    /**
     * @Route("/routine/sub/completed", name="user.sub.completed.routine")
     */
    public function userSubCompletedListRoutine(Request $request, RoutineService $routineService, RoutineSelectionService $routineSelectionService): Response
    {
        $expert = $request->query->get('expert');
        $type = $request->query->get('type');
        $page = $request->query->getInt('page', 1);

        if ($expert || $type) {
            $page = 1;
            $request->query->remove('page');
        }

        if ($type && $type != 'Any') {
            $type = $routineService->getTypeById($type);
        } else {
            $type = null;
        }

        $user = $this->getUser();
        $routines = $routineSelectionService->searchRoutine($expert, $type, $user, $page, RoutineSelection::STATUS_COMPLETED);

        $types = $routineService->getRoutineTypes();

        return $this->render('routine/user.sub.complited.list.html.twig', [
            'routines' => $routines,
            'types' => $types,
        ]);
    }

    /**
     * @Route("/routine/sub/{id}/show", name="user.sub.routine.show.one")
     */
    public function userSubRoutineShow(int $id, RoutineSelectionService $routineSelectionService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception('Error. User not found.');
        }

        $routine = $routineSelectionService->getRoutine($user, $id);

        return $this->render('routine/user.sub.routine.show.html.twig', [
            'routine' => $routine,
        ]);
    }

    /**
     * @Route("/routine/{id}/sub", name="user.sub.routine")
     */
    public function userSubRoutine(int $id, RoutineService $routineService, RoutineSelectionService $routineSelectionService): Response
    {
        $routine = $routineService->getRoutineById($id);
        if (!$routine) {
            return new Response(false);
        }

        $user = $this->getUser();
        if (!$user) {
            return new Response(false);
        }

        $result = $routineSelectionService->userSubsToRoutine($routine, $user);

        return new Response($result);
    }

    /**
     * @Route("/routine/sub/{id}/unsub", name="user.unsub.routine")
     */
    public function userUnsubRoutine(int $id, RoutineSelectionService $routineSelectionService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new Response(false);
        }

        $routineSelection = $routineSelectionService->getRoutine($user, $id);
        if (!$routineSelection) {
            return new Response(false);
        }

        $result = $routineSelectionService->userUnsubsRoutine($user, $routineSelection);
        return new Response($result);
    }
}
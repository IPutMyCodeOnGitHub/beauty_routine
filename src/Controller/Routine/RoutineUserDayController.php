<?php

namespace App\Controller\Routine;

use App\Entity\Routine;
use App\Entity\RoutineDay;
use App\Entity\RoutineSelection;
use App\Entity\RoutineType;
use App\Entity\RoutineUserDay;
use App\Entity\User;
use App\Form\RoutineDayType;
use App\Form\RoutineFormType;
use App\Services\RoutineSelectionService;
use App\Services\RoutineService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @Route("/profile")
 */
class RoutineUserDayController extends AbstractController
{
    /**
     * @Route("/routine/{id}/day/{dayId}/complete", name="user.routine.day.complete")
     */
    public function userRoutineDayCompleteAjax(int $id, int $dayId, RoutineSelectionService $routineSelectionService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new Response(false);
        }

        $routineDay = $routineSelectionService->getDay($user, $dayId, $id);
        if (!$routineDay) {
            return new Response(0);
        }

        $result = $routineSelectionService->completeDay($routineDay);
        return new Response($result);
    }

    /**
     * @Route("/routine/{id}/day/{dayId}/edit", name="user.routine.day.edit")
     */
    public function userRoutineDayEdit(int $id, int $dayId, RoutineSelectionService $routineSelectionService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new \Exception('Error. User not found.');;
        }

        $userDay = $routineSelectionService->getDay($user, $dayId, $id);

        $routineSelectionService->dayEdit($userDay);

        return $this->render('routine/user.day.edit.html.twig', [
            'day' => $userDay,
        ]);
    }

    /**
     * @Route("/routine/{id}/day/{dayId}/edit/product", name="user.routine.day.edit.list.product")
     */
    public function listProductForDay(int $id, int $dayId, RoutineSelectionService $routineSelectionService): Response
    {
        $user = $this->getUser();
        $userDay = $routineSelectionService->getDay($user, $dayId, $id);
        
        return new Response(false);
    }
}
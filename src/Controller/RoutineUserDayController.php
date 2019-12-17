<?php

namespace App\Controller;

use App\Entity\Routine;
use App\Entity\RoutineDay;
use App\Entity\RoutineSelection;
use App\Entity\RoutineType;
use App\Entity\RoutineUserDay;
use App\Entity\User;
use App\Form\RoutineDayType;
use App\Form\RoutineFormType;
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
    public function userRoutineDayCompleteAjax(int $id, int $dayId): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return new Response(0);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $routineDay = $entityManager
            ->getRepository(RoutineUserDay::class)
            ->getDayById($user, $dayId, $id);

        if (!$routineDay) {
            return new Response(0);
        }

        $routineDay->setIsCompleted(true);
        $routineDay->setDateCompleted(new \DateTime());

        $routineSelection = $routineDay->getRoutineSelection();
        $daysCompleted = $routineSelection->getDaysCompleted();
        if ($daysCompleted){
            $routineSelection->setDaysCompleted($daysCompleted + 1);
        } else {
            $routineSelection->setDaysCompleted(1);
        }

        $entityManager->persist($routineDay);
        $entityManager->persist($routineSelection);
        try{
            $entityManager->flush();
            return new Response(1);
        } catch(\Exception $e) {
            return new Response(0);
        }

    }

    /**
     * @Route("/routine/{id}/day/{dayId}/edit", name="user.routine.day.edit")
     */
    public function userRoutineDayEdit(int $id, int $dayId): Response
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $userDay = $entityManager->getRepository(RoutineUserDay::class)
            ->getDayById($user, $dayId, $id);

        if ($userDay->getIsChanged() == false || $userDay->getIsChanged() == null) {
            $products = $userDay->getRoutineDay()->getProducts();

            foreach ($products as $product) {
                $userDay->addProduct($product);
            }
            $userDay->setIsChanged(true);
            $entityManager->persist($userDay);
            $entityManager->flush();
        }

        return $this->render('routine/user.day.edit.html.twig', [
            'day' => $userDay,
        ]);
    }

    /**
     * @Route("/routine/{id}/day/{dayId}/edit/product", name="user.routine.day.edit.list.product")
     */
    public function listProductForDay(int $id, int $dayId): Response
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();
        $userDay = $entityManager->getRepository(RoutineUserDay::class)
            ->getDayById($user, $dayId, $id);
        return new Response(0);
    }
}
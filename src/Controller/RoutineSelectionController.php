<?php

namespace App\Controller;

use App\Entity\Routine;
use App\Entity\RoutineSelection;
use App\Entity\RoutineType;
use App\Entity\RoutineUserDay;
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
    public function userSubListRoutine(Request $request): Response
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

        $user = $this->getUser();

        $routines = $entityManager
            ->getRepository(RoutineSelection::class)
            ->searchRoutineSelectionPaginator($expert, $type, $user, $page);

        $types = $entityManager
            ->getRepository(RoutineType::class)
            ->findAll();

        return $this->render('routine/user.sub.list.html.twig', [
            'routines' => $routines,
            'types' => $types,
        ]);
    }

    /**
     * @Route("/routine/sub/{id}/show", name="user.sub.routine.show.one")
     */
    public function userSubRoutineShow(int $id, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception('Error. User not found.');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $routine = $entityManager
            ->getRepository(RoutineSelection::class)
            ->getUserRoutine($user, $id);

        return $this->render('routine/user.sub.routine.show.html.twig', [
            'routine' => $routine,
        ]);
    }

    /**
     * @Route("/routine/{id}/sub", name="user.sub.routine")
     */
    public function userSubRoutine(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $routine = $entityManager->getRepository(Routine::class)->find($id);

        $user = $this->getUser();

        if (!$routine) {
            return new Response(0);
        }
        if (!$user) {
            return new Response(0);
        }

        $routine->addSubscriber($user);
        $entityManager->persist($routine);

        $routineSelection = new RoutineSelection();
        $routineSelection->setParentRoutine($routine);
        $routineSelection->setUser($user);
        $routineSelection->setStatus(RoutineSelection::STATUS_ACTIVE);
        $entityManager->persist($routineSelection);

        try{
            $entityManager->flush();
            foreach ($routine->getRoutineDays() as $routineDay) {
                $routineUserDay = new RoutineUserDay();
                $routineUserDay->setRoutineSelection($routineSelection);
                $routineUserDay->setRoutineDay($routineDay);
                $entityManager->persist($routineUserDay);
            }
            $entityManager->flush();
            return new Response(1);
        } catch(\Exception $e) {
            return new Response(0);
        }
    }

    /**
     * @Route("/routine/sub/{id}/unsub", name="user.unsub.routine")
     */
    public function userUnsubRoutine(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $routineSelection = $entityManager->getRepository(RoutineSelection::class)->find($id);

        $user = $this->getUser();

        if (!$routineSelection) {
            return new Response(0);
        }
        if (!$user) {
            return new Response(0);
        }

        $routineSelection->setStatus(RoutineSelection::STATUS_UNSUB);
        $entityManager->persist($routineSelection);

        $routine = $routineSelection->getParentRoutine();
        $routine->removeSubscriber($user);
        $entityManager->persist($routine);

        try{
            $entityManager->flush();
            return new Response(1);
        } catch(\Exception $e) {
            return new Response(0);
        }
    }
}
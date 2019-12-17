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
    public function listRoutines(Request $request): Response
    {
        $expert = $this->getUser();
        if (!$expert) {
            throw $this->createNotFoundException('The expert does not exist');
        }
        $entityManager = $this->getDoctrine()->getManager();

        $status = $request->query->get('status');
        $type = $request->query->get('type');
        $page = $request->query->getInt('page', 1);

        if ($type || $status) {
            $page = 1;
            $request->query->remove('page');
        }

        if ($type || isset($type)) {
            $type = $entityManager->getRepository(RoutineType::class)->find($type);
        }

        $routines = $entityManager
            ->getRepository(Routine::class)
            ->searchRoutinePaginator($expert->getName(), $type, $page, null, $status);

        $types = $entityManager->getRepository(RoutineType::class)->findAll();

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
    public function delete(int $id, Request $request, RoutineService $routineService): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $routine = $entityManager->getRepository(Routine::class)->find($id);

        if (!$routine) {
            return new Response(0);
        }

        $entityManager->remove($routine);
        try {
            $entityManager->flush();
            return new Response(1);
        } catch(\Exception $e) {
            return new Response(0);
        }
    }

    /**
     * @Route("/expert/routine/{id}/edit", name="expert.routine.edit")
     */
    public function edit(Request $request, int $id, RoutineService $routineService): Response
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
            $result = $routineService->editRoutine($form, $routine);
            if ($result) {
                $this->addFlash('success', 'Routine updated!');
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
    public function activateRoutineAjax(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $routine = $entityManager->getRepository(Routine::class)->find($id);

        if (!$routine) {
            return new Response(0);
        }

        $routine->setStatus(Routine::STATUS_ACTIVE);
        $entityManager->persist($routine);


    }
    /**
     * @Route("/expert/routine/{id}/deactivate", name="expert.routine.deactivate")
     */
    public function deactivateRoutineAjax(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $routine = $entityManager->getRepository(Routine::class)->find($id);

        if (!$routine) {
            return new Response(0);
        }

        $routine->setStatus(Routine::STATUS_DISABLED);
        $entityManager->persist($routine);

        try {
            $entityManager->flush();
            return new Response(1);
        } catch(\Exception $e) {
            return new Response(0);
        }
    }


    /**
     * @Route("/routine/", name="user.routine")
     */
    public function userListRoutine(Request $request): Response
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
            ->searchRoutinePaginator($expert, $type, $page, null, Routine::STATUS_ACTIVE);
        $user = $this->getUser();

        $types = $entityManager->getRepository(RoutineType::class)->findAll();
        return $this->render('routine/user.list.html.twig', [
            'routines' => $routines,
            'user' => $user,
            'types' => $types,
        ]);
    }

    /**
     * @Route("/routine/show/{id}", name="user.routine.show")
     */
    public function userRoutineShow(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $routine = $entityManager->getRepository(Routine::class)->find($id);
        $user = $this->getUser();

        return $this->render('routine/user.routine.show.html.twig', [
            'routine' => $routine,
            'user' => $user
        ]);
    }
}

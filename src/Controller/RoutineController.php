<?php

namespace App\Controller;

use App\Entity\Routine;
use App\Entity\RoutineDay;
use App\Entity\RoutineType;
use App\Entity\User;
use App\Form\RoutineDayType;
use App\Form\RoutineFormType;
use App\Services\UploaderHelper;
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
    public function listRoutines(): Response
    {
        /**
         * @var User $expert
         */
        $expert = $this->getUser();

        if (!$expert) {
            throw $this->createNotFoundException('The expert does not exist');
        }

        return $this->render('routine/list.html.twig', [
            'expert' => $expert,
        ]);
    }

    /**
     * @Route("/expert/routine/create", name="expert.routine.create")
     */
    public function create(Request $request, UploaderHelper $uploaderHelper): Response
    {
        $routine = new Routine();
        $form = $this->createForm(RoutineFormType::class, $routine);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photo = $form['photo']->getData();
            $photoName = $uploaderHelper->uploadFile($photo, UploaderHelper::ROUTINE_PHOTO_PATH);

            if ($photo) {
                $routine->setPhoto(UploaderHelper::ROUTINE_PHOTO_PATH . $photoName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $routine->setStatus(Routine::STATUS_DRAFT);
            $routine->setUser($this->getUser());

            $entityManager->persist($routine);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Routine added!');
            } catch (\Exception $e) {
                if ($photoName != '') {
                    $uploaderHelper->deleteFile($photoName, UploaderHelper::CERTIFICATE_PATH);
                }
                $this->addFlash('danger', 'Sorry, that was an error.');
            }
        }

        return $this->render('routine/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/expert/routine/edit/{id}", name="expert.routine.edit")
     */
    public function edit(Request $request, int $id, UploaderHelper $uploaderHelper): Response
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
            $photo = $form['photo']->getData();
            $photoName = $uploaderHelper->uploadFile($photo, UploaderHelper::ROUTINE_PHOTO_PATH);
            if ($photo) {
                if ($routine->getPhoto()){
                    $uploaderHelper->deleteFile($routine->getPhoto(), '');
                }
                $routine->setPhoto(UploaderHelper::ROUTINE_PHOTO_PATH . $photoName);
            }

            $entityManager->persist($routine);
            try {
                $entityManager->flush();
                $this->addFlash('success', 'Routine updated!');
            } catch (\Exception $e) {
                if ($photoName != '') {
                    $uploaderHelper->deleteFile($photoName, UploaderHelper::CERTIFICATE_PATH);
                }
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
     * @Route("/expert/routine/edit/{id}/day/{dayId}", name="expert.routine.day.edit")
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
     * @Route("/expert/routine/edit/{id}/day/{dayId}/delete", name="expert.routine.day.delete")
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
            ->searchRoutinePaginator($expert, $type, $page, null);
        $user = $this->getUser();

        $types = $entityManager->getRepository(RoutineType::class)->findAll();
        return $this->render('routine/user.list.html.twig', [
            'routines' => $routines,
            'user' => $user,
            'types' => $types,
        ]);
    }

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
            ->getRepository(Routine::class)
            ->searchRoutinePaginator($expert, $type, $page, $user);

        $types = $entityManager->getRepository(RoutineType::class)->findAll();

        return $this->render('routine/user.sub.list.html.twig', [
            'routines' => $routines,
            'types' => $types,
        ]);
    }

    /**
     * @Route("/routine/{id}", name="user.routine.show")
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
//            $this->addFlash('danger', 'Sorry, routine doesn\'t exists.');
//            return $this->redirectToRoute('user.routine');
        }
        if (!$user) {
            return new Response(0);
//            $this->addFlash('danger', 'Sorry, user doesn\'t exists.');
//            return $this->redirectToRoute('user.routine');
        }

        $routine->addSubscriber($user);
        $entityManager->persist($routine);

        try{
            $entityManager->flush();
            return new Response(1);
//            $this->addFlash('success', 'You are subscribed.');
        } catch(\Exception $e) {
            return new Response(0);
//            $this->addFlash('danger', 'Sorry, error.');
        }
    }

}

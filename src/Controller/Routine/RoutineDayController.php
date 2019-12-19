<?php

namespace App\Controller\Routine;

use App\Entity\Product;
use App\Entity\ProductType;
use App\Entity\Routine;
use App\Entity\RoutineDay;
use App\Entity\RoutineSelection;
use App\Entity\RoutineType;
use App\Entity\User;
use App\Form\RoutineDayType;
use App\Form\RoutineFormType;
use App\Services\ProductService;
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
        $routine = $routineService->getRoutineById($id);

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
        $routineDay = $routineService->getRoutineDayById($dayId);

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
    public function deleteDay(Request $request, int $id, int $dayId, RoutineService $routineService): Response
    {
        $routineDay = $routineService->getRoutineDayById($dayId);

        if (!$routineDay) {
            $this->addFlash('danger', 'Sorry, day doesn\'t exists.');
            return new Response(false);
        }

        $result = $routineService->deleteDay($routineDay);

        return new Response($result);
    }

    /**
     * @Route("/expert/routine/{id}/day/{dayId}/product", name="expert.routine.day.list.product")
     */
    public function listProductForDay(int $id, int $dayId, Request $request, RoutineService $routineService, ProductService $productService): Response
    {
        $routineDay = $routineService->getRoutineDayById($dayId);

        $type = $request->query->get('type');
        $name = $request->query->get('name');
        $page = $request->query->getInt('page', 1);

        if ($type || $name) {
            $page = 1;
            $request->query->remove('page');
        }

        if ($type || isset($type)) {
            $type = $routineService->getTypeById($type);
        }

        $products = $productService->search($type, $name, null, $page);

        $types = $routineService->getRoutineTypes();

        return $this->render('routine/product.add.html.twig', [
            'types' => $types,
            'products' => $products,
            'day' => $routineDay,
        ]);
    }

    /**
     * @Route("/expert/routine/{id}/day/{dayId}/product/{prodId}", name="expert.routine.day.add.product")
     */
    public function addProductInDay(int $id, int $dayId, int $prodId, ProductService $productService, RoutineService $routineService): Response
    {
        $product = $productService->findProductById($prodId);

        if (!$product) {
            throw $this->createNotFoundException('The product does not exist');
        }

        $day = $routineService->getRoutineDayById($dayId);
        if (!$day) {
            throw $this->createNotFoundException('Day does not exist');
        }

        $result = $routineService->addProductInDay($day, $product, $id);
        $this->addFlash($result['type'], $result['message']);

        return $this->redirectToRoute($result['route'], $result['params']);

    }

    /**
     * @Route("/expert/routine/{id}/day/{dayId}/product/{prodId}/delete", name="expert.routine.day.delete.product")
     */
    public function deleteProductInDay(int $id, int $dayId, int $prodId, ProductService $productService, RoutineService $routineService): Response
    {
        $product = $productService->findProductById($prodId);
        if (!$product) {
            return new Response(false);
        }

        $day = $routineService->getRoutineDayById($dayId);
        if (!$day) {
            return new Response(false);
        }

        $result = $routineService->deleteProductInDay($day, $product);

        return new Response($result);
    }
}
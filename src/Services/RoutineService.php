<?php

namespace App\Services;

use App\Entity\Product;
use App\Entity\Routine;
use App\Entity\RoutineDay;
use App\Entity\RoutineType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\Form;

class RoutineService
{
    private $uploaderHelper,
        $em;

    public function __construct(EntityManagerInterface $em, UploaderHelper $uploaderHelper)
    {
        $this->em = $em;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function createRoutineForm(User $user, Form $form, Routine $routine): ?Routine
    {
        $photo = $form['photo']->getData();
        $photoName = $this->uploaderHelper->uploadFile($photo, UploaderHelper::ROUTINE_PHOTO_PATH);

        if ($photo) {
            $routine->setPhoto(UploaderHelper::ROUTINE_PHOTO_PATH . $photoName);
        }

        $routine->setStatus(Routine::STATUS_DRAFT);
        $routine->setUser($user);

        $this->em->persist($routine);
        try {
            $this->em->flush();
            return $routine;
        } catch (\Exception $e) {
            if ($photoName != '') {
                $this->uploaderHelper->deleteFile($photoName, UploaderHelper::CERTIFICATE_PATH);
            }
            return null;
        }
    }

    public function createDay(RoutineDay $routineDay, Routine $routine): ?RoutineDay
    {
        $order = count($routine->getRoutineDays()) + 1;
        $routineDay->setDayOrder($order);
        $routineDay->setRoutine($routine);
        $this->em->persist($routineDay);

        if ($routine->getStatus() == Routine::STATUS_DRAFT) {
            $routine->setStatus(Routine::STATUS_DISABLED);
            $this->em->persist($routine);
        }

        try {
            $this->em->flush();
            return $routineDay;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function editRoutine(Form $form, Routine $routine): ?Routine
    {
        $photo = $form['photo']->getData();
        if ($photo) {
            $photoName = $this->uploaderHelper->uploadFile($photo, UploaderHelper::ROUTINE_PHOTO_PATH);
            if ($routine->getPhoto()){
                $this->uploaderHelper->deleteFile($routine->getPhoto(), '');
            }
            $routine->setPhoto(UploaderHelper::ROUTINE_PHOTO_PATH . $photoName);
        }

        $this->em->persist($routine);
        try {
            $this->em->flush();
            return $routine;
        } catch (\Exception $e) {
            if (isset($photoName)) {
                $this->uploaderHelper->deleteFile($photoName, UploaderHelper::ROUTINE_PHOTO_PATH);
            }
            return null;
        }
    }

    public function editDay(RoutineDay $routineDay): ?RoutineDay
    {
        $this->em->persist($routineDay);
        try {
            $this->em->flush();
            return $routineDay;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function deleteRoutine(Routine $routine): bool
    {
        $this->em->remove($routine);
        try {
            $this->em->flush();
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deleteDay(RoutineDay $routineDay): bool
    {
        foreach ($routineDay->getRoutine()->getRoutineDays() as $day) {
            if ($day->getDayOrder() >= $routineDay->getDayOrder()) {
                $day->setDayOrder($day->getDayOrder() - 1);
                $this->em->persist($day);
            }
        }

        $this->em->remove($routineDay);
//        $this->em->persist($routineDay);

        try {
            $this->em->flush();
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function deleteProductInDay(RoutineDay $day, Product $product): bool
    {
        $day->removeProduct($product);
        $this->em->persist($day);

        try {
            $this->em->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function activateRoutine(Routine $routine): bool
    {
        $routine->setStatus(Routine::STATUS_ACTIVE);
        $this->em->persist($routine);
        try {
            $this->em->flush();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function deactivateRoutine(Routine $routine): bool
    {
        $routine->setStatus(Routine::STATUS_DISABLED);
        $this->em->persist($routine);

        try {
            $this->em->flush();
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function searchRoutine(?String $expert,
                                  ?RoutineType $type,
                                  int $page = 1,
                                  ?User $subscriber,
                                  ?string $status): ?PaginationInterface
    {
        return $this->em
            ->getRepository(Routine::class)
            ->searchRoutinePaginator($expert, $type, $page, null, $status);
    }

    public function getRoutineTypes(): array
    {
        return $this->em->getRepository(RoutineType::class)->findAll();
    }

    public function getTypeById(int $id): ?RoutineType
    {
        return $this->em->getRepository(RoutineType::class)->find($id);
    }

    public function getRoutineById(int $id): ?Routine
    {
        return $this->em->getRepository(Routine::class)->find($id);
    }

    public function getRoutineDayById(int $id): ?RoutineDay
    {
        return $this->em->getRepository(RoutineDay::class)->find($id);
    }

    public function addProductInDay(RoutineDay $day, Product $product, int $routineId): array
    {
        $day->addProduct($product);
        $this->em->persist($day);

        try {
            $this->em->flush();
            return [
                'type' => 'success',
                'message' => 'Product addded',
                'route' => 'expert.routine.day.list.product',
                'params' => [
                    'id' => $routineId,
                    'dayId' => $day->getId()
                ]
            ];
        } catch (\Exception $e) {
            return [
                'type' => 'danger',
                'message' => 'Error',
                'route' => 'expert.routine.day.list.product',
                'params' => [
                    'id' => $routineId,
                    'dayId' => $day->getId()
                ]
            ];
        }
    }
}
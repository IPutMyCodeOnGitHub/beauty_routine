<?php

namespace App\Services;

use App\Entity\Routine;
use App\Entity\RoutineSelection;
use App\Entity\RoutineType;
use App\Entity\RoutineUserDay;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Response;

class RoutineSelectionService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function searchRoutine(?String $expert,
                                  ?RoutineType $type,
                                  ?User $user,
                                  int $page,
                                  string $status = RoutineSelection::STATUS_ACTIVE): ?PaginationInterface
    {
        return $this->em
            ->getRepository(RoutineSelection::class)
            ->searchRoutineSelectionPaginator($expert, $type, $user, $page, $status);
    }

    public function getRoutine(User $user, int $id): ?RoutineSelection
    {
        return $this->em
            ->getRepository(RoutineSelection::class)
            ->getUserRoutine($user, $id);
    }

    public function getDay(User $user, int $id, int $routineId): ?RoutineUserDay
    {
        return $this->em
            ->getRepository(RoutineUserDay::class)
            ->getDayById($user, $id, $routineId);
    }

    public function userSubsToRoutine(Routine $routine, User $user): string
    {
        $routine->addSubscriber($user);
        $this->em->persist($routine);

        $routineSelection = new RoutineSelection();
        $routineSelection->setParentRoutine($routine);
        $routineSelection->setUser($user);
        $routineSelection->setStatus(RoutineSelection::STATUS_ACTIVE);
        $this->em->persist($routineSelection);

        try{
            $this->em->flush();
            foreach ($routine->getRoutineDays() as $routineDay) {
                $routineUserDay = new RoutineUserDay();
                $routineUserDay->setRoutineSelection($routineSelection);
                $routineUserDay->setRoutineDay($routineDay);
                $this->em->persist($routineUserDay);

                $routineSelection->addRoutineUserDay($routineUserDay);
                $this->em->persist($routineSelection);
                $this->em->persist($routineSelection);
            }
            $this->em->flush();
            return true;
        } catch(\Exception $e) {
            return false . $e->getMessage() . 'mes';
        }
    }

    public function userUnsubsRoutine(User $user, RoutineSelection $routineSelection): bool
    {
        $routineSelection->setStatus(RoutineSelection::STATUS_UNSUB);
        $this->em->persist($routineSelection);

        $routine = $routineSelection->getParentRoutine();
        $routine->removeSubscriber($user);
        $this->em->persist($routine);

        try{
            $this->em->flush();
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function completeDay(RoutineUserDay $routineDay)
    {
        $routineDay->setIsCompleted(true);
        $routineDay->setDateCompleted(new \DateTime());

        $routineSelection = $routineDay->getRoutineSelection();
        $daysCompleted = $routineSelection->getDaysCompleted();

        if ($daysCompleted){
            $routineSelection->setDaysCompleted($daysCompleted + 1);
        } else {
            $routineSelection->setDaysCompleted(1);
        }

        if ($routineSelection->getDaysCompleted() == $routineSelection->getRoutineUserDays()->count()) {
            $routineSelection->setStatus(RoutineSelection::STATUS_COMPLETED);
            $routineSelection->getParentRoutine()->removeSubscriber($routineSelection->getUser());
        }

        $this->em->persist($routineDay);
        $this->em->persist($routineSelection);
        try{
            $this->em->flush();
            return true;
        } catch(\Exception $e) {
            return false;
        }
    }

    public function dayEdit(RoutineUserDay $userDay): bool
    {
        if ($userDay->getIsChanged() == false || $userDay->getIsChanged() == null) {
            $products = $userDay->getRoutineDay()->getProducts();

            foreach ($products as $product) {
                $userDay->addProduct($product);
            }
            $userDay->setIsChanged(true);
            $this->em->persist($userDay);
            try {
                $this->em->flush();
                return true;
            } catch(\Exception $e) {
                return false;
            }
        }

        return false;
    }

}
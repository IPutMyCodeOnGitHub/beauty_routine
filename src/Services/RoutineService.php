<?php

namespace App\Services;

use App\Entity\Routine;
use App\Entity\RoutineDay;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;

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
                $this->uploaderHelper->deleteFile($photoName, UploaderHelper::CERTIFICATE_PATH);
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
}
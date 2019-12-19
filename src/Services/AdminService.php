<?php

namespace App\Services;

use App\Entity\RoutineType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class AdminService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    public function allUsers(): array
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findAll();
    }

    public function getAdmins(?string $search, int $page = 1): ?PaginationInterface
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findSearchAdminsPaginator($search, $page);
    }

    public function ajaxExpertDelete(int $id): bool
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            return false;
        }

        $this->entityManager->remove($user);

        try {
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function routineTypesCreate(RoutineType $type): array
    {
        $this->entityManager->persist($type);
        try {
            $this->entityManager->flush();
            return ['type' => 'success',
                'message' => 'New type added'];
        } catch (\Exception $e) {
            return ['type' => 'danger',
                'message' => 'Error: ' . $e];
        }
    }

    public function routineTypes(): ?PaginationInterface
    {
        $types = $this->entityManager
            ->getRepository(RoutineType::class)
            ->getAllTypes();
        return $types;
    }

    public function routineTypesAjaxDelete(int $id): bool
    {
        $type = $this->entityManager->getRepository(RoutineType::class)->find($id);

        if (!$type) {
            return false;
        }

        $this->entityManager->remove($type);
        try {
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function routineTypesEdit(Form $form, RoutineType $type, Request $request): ?array
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($type);
            try {
                $this->entityManager->flush();
                return [
                    'type' => 'success',
                    'message' => 'Type updated',
                    'route' => 'admin.type'
                ];
            } catch (\Exception $e) {
                return [
                    'type' => 'danger',
                    'message' => 'Error: ' . $e,
                    'route' => 'admin.type'
                ];
            }
        }

        return null;
    }

    public function searchUser(?string $search, ?bool $valid, int $page = 1): ?PaginationInterface
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findSearchUserPaginator($search, $valid, $page);
    }

    public function searchUserById(int $id): ?User
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->find($id);
    }

    public function searchExpert(?string $search, ?bool $active, int $page = 1): ?PaginationInterface
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findSearchExpertPaginator($search, $active, $page);
    }

    public function ajaxUserDelete(int $id): bool
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            return false;
        }

        $this->entityManager->remove($user);
        try {
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
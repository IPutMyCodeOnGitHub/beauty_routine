<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Knp\Component\Pager\Paginator;

class PaginatorService
{
    const DEFAULTE_COUNT = 10;

    private $paginator;
    private $pageCount;

    public function __construct(Paginator $paginator)
    {
        $this->paginator = $paginator;
        $this->pageCount = PaginatorService::DEFAULTE_COUNT;
    }

    public function setPageCount(int $pageCount)
    {
        $this->pageCount = $pageCount;
    }

    public function findUserByRolePaginator(EntityManager $entityManager, int $page)
    {
        $queryBuilder = $entityManager
            ->getRepository(User::class)
            ->getQueryBuilderFindByRole('ROLE_EXPERT', 'ROLE_VALID_EXPERT');

        return $pagination = $this->paginator->paginate(
            $queryBuilder,
            $page,
            $this->pageCount
        );
    }
}
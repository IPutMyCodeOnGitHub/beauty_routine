<?php

namespace App\Repository;

use App\Entity\RoutineType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;

class RoutineTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Paginator  $paginator)
    {
        parent::__construct($registry, RoutineType::class);
        $this->paginator = $paginator;
    }

    public function getAllTypes(int $page = 1, int $countObj = 10): ?PaginationInterface
    {
        $queryBuilder = $this->createQueryBuilder('rt')->orderBy('rt.id', 'ASC');

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $countObj
        );
    }

}

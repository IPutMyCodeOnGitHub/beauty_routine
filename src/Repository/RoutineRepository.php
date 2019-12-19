<?php

namespace App\Repository;

use App\Entity\Routine;
use App\Entity\RoutineType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;

/**
 * @method Routine|null find($id, $lockMode = null, $lockVersion = null)
 * @method Routine|null findOneBy(array $criteria, array $orderBy = null)
 * @method Routine[]    findAll()
 * @method Routine[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoutineRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, Paginator $paginator)
    {
        parent::__construct($registry, Routine::class);
        $this->paginator = $paginator;
    }

    public function getQueryBuilderSearchRoutines(?String $expert, ?RoutineType $type, ?User $subscriber, ?string $status): QueryBuilder
    {
        $query = $this->createQueryBuilder('r');
        $query->orderBy('r.id', 'ASC');

        if ($expert) {
            $query->join("r.user", 'user')
                ->andWhere($query->expr()->like('user.name', ':expert'))
                ->setParameter('expert', '%'.$expert.'%');
        }

        if ($subscriber) {
            $query->andWhere(':subscriber MEMBER OF r.subscriber')
                ->setParameter('subscriber', $subscriber);
        }

        if ($type) {
            $query->andWhere('r.type = :type')
                ->setParameter('type', $type);
        }

        if ($status && in_array($status, [Routine::STATUS_DISABLED, Routine::STATUS_BLOCKED, Routine::STATUS_ACTIVE, Routine::STATUS_DRAFT])) {
            $query->andwhere('r.status = :status')
                ->setParameter('status', $status);
        }

        return $query;
    }

    public function searchRoutinePaginator(
        ?String $expert,
        ?RoutineType $type,
        int $page = 1,
        ?User $subscriber,
        ?string $status,
        int $countObj = 10): ?PaginationInterface
    {
        $queryBuilder = $this->getQueryBuilderSearchRoutines($expert, $type, $subscriber, $status);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $countObj
        );
    }
}

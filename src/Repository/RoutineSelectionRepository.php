<?php

namespace App\Repository;

use App\Entity\Routine;
use App\Entity\RoutineSelection;
use App\Entity\RoutineType;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\Paginator;

/**
 * @method RoutineSelection|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoutineSelection|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoutineSelection[]    findAll()
 * @method RoutineSelection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoutineSelectionRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, Paginator  $paginator)
    {
        parent::__construct($registry, RoutineSelection::class);
        $this->paginator = $paginator;
    }

    public function getQueryBuilderSearchRoutinesSelection(?String $expert, ?RoutineType $type, ?User $subscriber, string $status = "active"): QueryBuilder
    {
        $query = $this->createQueryBuilder('rs');
        $query
            ->andWhere('rs.status = :status')
            ->setParameter('status', $status)
            ->orderBy('rs.id', 'ASC')
            ->join("rs.parentRoutine", 'pr');

        if ($expert) {
            $query
                ->join("pr.user", 'pru')
                ->andWhere($query->expr()->like('pru.name', ':expert'))
                ->setParameter('expert', '%'.$expert.'%');
        }

        if ($subscriber) {
            $query->andWhere(' rs.user = :subscriber')
                ->setParameter('subscriber', $subscriber);
        }

        if ($type) {
            $query->andWhere('pr.type = :type')
                ->setParameter('type', $type);
        }

        return $query;
    }

    public function searchRoutineSelectionPaginator(
        ?String $expert,
        ?RoutineType $type,
        ?User $subscriber,
        int $page = 1,
        string $status = RoutineSelection::STATUS_ACTIVE,
        int $countObj = 10
    ): ?PaginationInterface
    {
        $queryBuilder = $this->getQueryBuilderSearchRoutinesSelection($expert, $type, $subscriber, $status);

        return $this->paginator->paginate(
            $queryBuilder,
            $page,
            $countObj
        );
    }

    public function userRoutineSelection(?String $expert,
                                         ?RoutineType $type,
                                         ?User $subscriber): array
    {
        $queryBuilder = $this->getQueryBuilderSearchRoutinesSelection($expert, $type, $subscriber);
        return $queryBuilder->getQuery()->getResult();
    }

    public function getUserRoutine(User $user, int $id): ?RoutineSelection
    {
        $query = $this->createQueryBuilder('rs')
            ->orderBy('rs.id', 'ASC');
        $query
            ->andWhere('rs.id = :id')
            ->setParameter('id', $id)
            ->andWhere('rs.user = :user')
            ->setParameter('user', $user)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }
}

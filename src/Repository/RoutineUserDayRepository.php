<?php

namespace App\Repository;

use App\Entity\RoutineUserDay;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RoutineUserDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoutineUserDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoutineUserDay[]    findAll()
 * @method RoutineUserDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoutineUserDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoutineUserDay::class);
    }

    public function getDayById(User $user, int $id, int $routineId): RoutineUserDay
    {
        $query = $this->createQueryBuilder('rd');
        $query->andWhere('rd.id = :id')
            ->setParameter('id', $id)
            ->join('rd.routineSelection', 'rs')
            ->andWhere('rs.user = :user')
            ->setParameter('user', $user)
            ->andWhere('rs.id = :routineId')
            ->setParameter('routineId', $routineId)
        ;

        return $query->getQuery()->getOneOrNullResult();
    }
}

<?php

namespace App\Repository;

use App\Entity\RoutineUserDay;
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

    // /**
    //  * @return RoutineUserDay[] Returns an array of RoutineUserDay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RoutineUserDay
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

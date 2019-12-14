<?php

namespace App\Repository;

use App\Entity\RoutineType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RoutineType|null find($id, $lockMode = null, $lockVersion = null)
 * @method RoutineType|null findOneBy(array $criteria, array $orderBy = null)
 * @method RoutineType[]    findAll()
 * @method RoutineType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoutineTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoutineType::class);
    }

    // /**
    //  * @return RoutineType[] Returns an array of RoutineType objects
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
    public function findOneBySomeField($value): ?RoutineType
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

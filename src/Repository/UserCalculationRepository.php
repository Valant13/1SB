<?php

namespace App\Repository;

use App\Entity\UserCalculation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserCalculation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCalculation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCalculation[]    findAll()
 * @method UserCalculation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCalculationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCalculation::class);
    }

    // /**
    //  * @return UserCalculation[] Returns an array of UserCalculation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserCalculation
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

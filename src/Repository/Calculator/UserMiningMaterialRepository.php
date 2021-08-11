<?php

namespace App\Repository\Calculator;

use App\Entity\Calculator\UserMiningMaterial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMiningMaterial|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMiningMaterial|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMiningMaterial[]    findAll()
 * @method UserMiningMaterial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMiningMaterialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMiningMaterial::class);
    }

    // /**
    //  * @return UserMiningMaterial[] Returns an array of UserMiningMaterial objects
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
    public function findOneBySomeField($value): ?UserMiningMaterial
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

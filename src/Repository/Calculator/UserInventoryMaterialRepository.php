<?php

namespace App\Repository\Calculator;

use App\Entity\Calculator\UserInventoryMaterial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInventoryMaterial|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInventoryMaterial|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInventoryMaterial[]    findAll()
 * @method UserInventoryMaterial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInventoryMaterialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInventoryMaterial::class);
    }

    // /**
    //  * @return UserInventoryMaterial[] Returns an array of UserInventoryMaterial objects
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
    public function findOneBySomeField($value): ?UserInventoryMaterial
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

<?php

namespace App\Repository;

use App\Entity\UserInterestMaterial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInterestMaterial|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInterestMaterial|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInterestMaterial[]    findAll()
 * @method UserInterestMaterial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInterestMaterialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInterestMaterial::class);
    }

    // /**
    //  * @return UserInterestMaterial[] Returns an array of UserInterestMaterial objects
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
    public function findOneBySomeField($value): ?UserInterestMaterial
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

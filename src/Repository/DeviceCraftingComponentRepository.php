<?php

namespace App\Repository;

use App\Entity\DeviceCraftingComponent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviceCraftingComponent|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviceCraftingComponent|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviceCraftingComponent[]    findAll()
 * @method DeviceCraftingComponent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceCraftingComponentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceCraftingComponent::class);
    }

    // /**
    //  * @return DeviceCraftingComponent[] Returns an array of DeviceCraftingComponent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DeviceCraftingComponent
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

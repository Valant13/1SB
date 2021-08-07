<?php

namespace App\Repository;

use App\Entity\DeviceCraftingExperience;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DeviceCraftingExperience|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeviceCraftingExperience|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeviceCraftingExperience[]    findAll()
 * @method DeviceCraftingExperience[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeviceCraftingExperienceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceCraftingExperience::class);
    }

    // /**
    //  * @return DeviceCraftingExperience[] Returns an array of DeviceCraftingExperience objects
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
    public function findOneBySomeField($value): ?DeviceCraftingExperience
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

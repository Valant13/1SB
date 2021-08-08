<?php

namespace App\Repository\Catalog;

use App\Entity\Catalog\ResearchPoint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ResearchPoint|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResearchPoint|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResearchPoint[]    findAll()
 * @method ResearchPoint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResearchPointRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResearchPoint::class);
    }

    // /**
    //  * @return ResearchPoint[] Returns an array of ResearchPoint objects
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
    public function findOneBySomeField($value): ?ResearchPoint
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

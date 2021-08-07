<?php

namespace App\Repository;

use App\Entity\ProductAuctionPrice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProductAuctionPrice|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductAuctionPrice|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductAuctionPrice[]    findAll()
 * @method ProductAuctionPrice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductAuctionPriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductAuctionPrice::class);
    }

    // /**
    //  * @return ProductAuctionPrice[] Returns an array of ProductAuctionPrice objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductAuctionPrice
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

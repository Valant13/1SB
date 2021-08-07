<?php

namespace App\Repository;

use App\Entity\UserInterestDevice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInterestDevice|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInterestDevice|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInterestDevice[]    findAll()
 * @method UserInterestDevice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInterestDeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInterestDevice::class);
    }

    // /**
    //  * @return UserInterestDevice[] Returns an array of UserInterestDevice objects
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
    public function findOneBySomeField($value): ?UserInterestDevice
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

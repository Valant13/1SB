<?php

namespace App\Repository\User;

use App\Entity\User\UserLogRecord;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserLogRecord|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLogRecord|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserLogRecord[]    findAll()
 * @method UserLogRecord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserLogRecordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserLogRecord::class);
    }

    // /**
    //  * @return UserLogRecord[] Returns an array of UserLogRecord objects
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
    public function findOneBySomeField($value): ?UserLogRecord
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

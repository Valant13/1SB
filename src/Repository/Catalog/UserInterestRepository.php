<?php

namespace App\Repository\Catalog;

use App\Entity\Catalog\UserInterest;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInterest|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInterest|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInterest[]    findAll()
 * @method UserInterest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInterestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInterest::class);
    }

    /**
     * @param User $user
     * @return UserInterest|null
     */
    public function findOneByUser(User $user): ?UserInterest
    {
        return $this->createQueryBuilder('ui')
            ->andWhere('ui.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

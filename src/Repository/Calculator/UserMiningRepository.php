<?php

namespace App\Repository\Calculator;

use App\Entity\Calculator\UserMining;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMining|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMining|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMining[]    findAll()
 * @method UserMining[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMiningRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMining::class);
    }

    /**
     * @param User $user
     * @return UserMining|null
     */
    public function findOneByUser(User $user): ?UserMining
    {
        return $this->createQueryBuilder('um')
            ->andWhere('um.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

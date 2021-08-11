<?php

namespace App\Repository\Calculator;

use App\Entity\Calculator\UserInventory;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserInventory|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserInventory|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserInventory[]    findAll()
 * @method UserInventory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserInventoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserInventory::class);
    }

    /**
     * @param User $user
     * @return UserInventory|null
     */
    public function findOneByUser(User $user): ?UserInventory
    {
        return $this->createQueryBuilder('ui')
            ->andWhere('ui.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

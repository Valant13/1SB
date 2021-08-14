<?php

namespace App\Repository\Calculator;

use App\Entity\Calculator\UserCalculation;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserCalculation|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserCalculation|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserCalculation[]    findAll()
 * @method UserCalculation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserCalculationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCalculation::class);
    }

    /**
     * @param User $user
     * @return UserCalculation|null
     */
    public function findOneByUser(User $user): ?UserCalculation
    {
        return $this->createQueryBuilder('uc')
            ->andWhere('uc.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}

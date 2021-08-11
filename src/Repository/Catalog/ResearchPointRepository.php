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

    /**
     * @return ResearchPoint[]
     */
    public function findOrderedBySortOrder(): array
    {
        return $this->createQueryBuilder('rp')
            ->orderBy('rp.sortOrder', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}

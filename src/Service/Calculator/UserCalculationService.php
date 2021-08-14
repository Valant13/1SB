<?php

namespace App\Service\Calculator;

use App\Entity\Calculator\UserCalculation;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserCalculationService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $event
     */
    public function persistUserCalculation(User $user, LifecycleEventArgs $event): void
    {
        $calculation = new UserCalculation();

        $calculation->setUser($user);

        $this->entityManager->persist($calculation);
        $this->entityManager->flush();
    }
}

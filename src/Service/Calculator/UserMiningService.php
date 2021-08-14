<?php

namespace App\Service\Calculator;

use App\Entity\Calculator\UserMining;
use App\Entity\Calculator\UserMiningMaterial;
use App\Entity\Catalog\Material;
use App\Entity\User\User;
use App\Repository\Calculator\UserMiningRepository;
use App\Repository\Catalog\MaterialRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;

class UserMiningService
{
    /**
     * @var UserMiningRepository
     */
    private $miningRepository;

    /**
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserMiningRepository $miningRepository
     * @param MaterialRepository $materialRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserMiningRepository $miningRepository,
        MaterialRepository $materialRepository
    ) {
        $this->miningRepository = $miningRepository;
        $this->materialRepository = $materialRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Material $material
     * @param LifecycleEventArgs $event
     */
    public function populateUserMiningWithMaterial(Material $material, LifecycleEventArgs $event): void
    {
        $minings = $this->miningRepository->findAll();

        foreach ($minings as $mining) {
            $miningMaterial = new UserMiningMaterial();

            $miningMaterial->setUserMining($mining);
            $miningMaterial->setMaterial($material);
        }
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $event
     */
    public function persistUserMining(User $user, LifecycleEventArgs $event): void
    {
        $mining = new UserMining();

        $mining->setUser($user);

        $materials = $this->materialRepository->findAll();
        foreach ($materials as $material) {
            $miningMaterial = new UserMiningMaterial();

            $miningMaterial->setMaterial($material);
            $miningMaterial->setUserMining($mining);

            $mining->addMaterial($miningMaterial);
        }

        $this->entityManager->persist($mining);
        $this->entityManager->flush();
    }
}

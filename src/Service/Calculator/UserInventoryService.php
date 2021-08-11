<?php

namespace App\Service\Calculator;

use App\Entity\Calculator\UserInventory;
use App\Entity\Calculator\UserInventoryMaterial;
use App\Entity\Catalog\Material;
use App\Entity\User\User;
use App\Repository\Calculator\UserInventoryRepository;
use App\Repository\Catalog\MaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class UserInventoryService
{
    /**
     * @var UserInventoryRepository
     */
    private $inventoryRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserInventoryRepository $inventoryRepository
     * @param MaterialRepository $materialRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserInventoryRepository $inventoryRepository,
        MaterialRepository $materialRepository
    ) {
        $this->inventoryRepository = $inventoryRepository;
        $this->entityManager = $entityManager;
        $this->materialRepository = $materialRepository;
    }

    /**
     * @param Material $material
     * @param LifecycleEventArgs $event
     */
    public function populateUserInventoriesWithMaterial(Material $material, LifecycleEventArgs $event): void
    {
        $inventories = $this->inventoryRepository->findAll();

        foreach ($inventories as $inventory) {
            $inventoryMaterial = new UserInventoryMaterial();

            $inventoryMaterial->setUserInventory($inventory);
            $inventoryMaterial->setMaterial($material);
            $inventoryMaterial->setQty(0);
        }
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $event
     */
    public function persistUserInventory(User $user, LifecycleEventArgs $event): void
    {
        $inventory = new UserInventory();

        $inventory->setUser($user);

        $materials = $this->materialRepository->findAll();
        foreach ($materials as $material) {
            $inventoryMaterial = new UserInventoryMaterial();

            $inventoryMaterial->setMaterial($material);
            $inventoryMaterial->setUserInventory($inventory);
            $inventoryMaterial->setQty(0);

            $inventory->addMaterial($inventoryMaterial);
        }

        $this->entityManager->persist($inventory);
        $this->entityManager->flush();
    }
}

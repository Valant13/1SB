<?php

namespace App\Service\Catalog;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\UserInterest;
use App\Entity\Catalog\UserInterestDevice;
use App\Entity\Catalog\UserInterestMaterial;
use App\Entity\User\User;
use App\Repository\Catalog\DeviceRepository;
use App\Repository\Catalog\MaterialRepository;
use App\Repository\Catalog\UserInterestRepository;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;

class UserInterestService
{
    /**
     * @var UserInterestRepository
     */
    private $interestRepository;

    /**
     * @var MaterialRepository
     */
    private $materialRepository;

    /**
     * @var DeviceRepository
     */
    private $deviceRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserInterestRepository $interestRepository
     * @param MaterialRepository $materialRepository
     * @param DeviceRepository $deviceRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserInterestRepository $interestRepository,
        MaterialRepository $materialRepository,
        DeviceRepository $deviceRepository
    ) {
        $this->interestRepository = $interestRepository;
        $this->materialRepository = $materialRepository;
        $this->deviceRepository = $deviceRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param Material[] $materials
     * @param User $user
     * @param bool $isExcluded
     * @return Material[]
     */
    public function filterMaterialsByExclusion(array $materials, User $user, bool $isExcluded = false): array
    {
        $interest = $this->interestRepository->findOneByUser($user);
        $excludedMaterialIds = $interest->getExcludedMaterialIds();

        $filteredMaterials = [];
        foreach ($materials as $material) {
            // Searching for excluded materials
            if ($isExcluded && in_array($material->getId(), $excludedMaterialIds)) {
                $filteredMaterials[] = $material;
            }

            // Searching for included materials
            if (!$isExcluded && !in_array($material->getId(), $excludedMaterialIds)) {
                $filteredMaterials[] = $material;
            }
        }

        return $filteredMaterials;
    }

    /**
     * @param Device[] $devices
     * @param User $user
     * @param bool $isExcluded
     * @return Device[]
     */
    public function filterDevicesByExclusion(array $devices, User $user, bool $isExcluded = false): array
    {
        $interest = $this->interestRepository->findOneByUser($user);
        $excludedMaterialIds = $interest->getExcludedMaterialIds();
        $excludedDeviceIds = $interest->getExcludedDeviceIds();

        $filteredDevices = [];
        foreach ($devices as $device) {
            // Searching for excluded devices
            if ($isExcluded) {
                if (in_array($device->getId(), $excludedDeviceIds)) {
                    $filteredDevices[] = $device;
                } else {
                    foreach ($device->getCraftingComponents() as $componentRecord) {
                        if (in_array($componentRecord->getMaterial()->getId(), $excludedMaterialIds)) {
                            $filteredDevices[] = $device;
                            break;
                        }
                    }
                }
            }

            // Searching for included devices
            if (!$isExcluded && !in_array($device->getId(), $excludedDeviceIds)) {
                $hasExcludedComponents = false;

                foreach ($device->getCraftingComponents() as $componentRecord) {
                    if (in_array($componentRecord->getMaterial()->getId(), $excludedMaterialIds)) {
                        $hasExcludedComponents = true;
                        break;
                    }
                }

                if (!$hasExcludedComponents) {
                    $filteredDevices[] = $device;
                }
            }
        }

        return $filteredDevices;
    }

    /**
     * @param Material $material
     * @param LifecycleEventArgs $event
     */
    public function populateUserInterestsWithMaterial(Material $material, LifecycleEventArgs $event): void
    {
        $interests = $this->interestRepository->findAll();

        foreach ($interests as $interest) {
            $interestMaterial = new UserInterestMaterial();

            $interestMaterial->setUserInterest($interest);
            $interestMaterial->setMaterial($material);
        }
    }

    /**
     * @param Device $device
     * @param LifecycleEventArgs $event
     */
    public function populateUserInterestsWithDevice(Device $device, LifecycleEventArgs $event): void
    {
        $interests = $this->interestRepository->findAll();

        foreach ($interests as $interest) {
            $interestDevice = new UserInterestDevice();

            $interestDevice->setUserInterest($interest);
            $interestDevice->setDevice($device);

            $interest->addDevice($interestDevice);
        }
    }

    /**
     * @param User $user
     * @param LifecycleEventArgs $event
     */
    public function persistUserInterest(User $user, LifecycleEventArgs $event): void
    {
        $interest = new UserInterest();

        $interest->setUser($user);

        $materials = $this->materialRepository->findAll();
        foreach ($materials as $material) {
            $interestMaterial = new UserInterestMaterial();

            $interestMaterial->setMaterial($material);
            $interestMaterial->setUserInterest($interest);

            $interest->addMaterial($interestMaterial);
        }

        $devices = $this->deviceRepository->findAll();
        foreach ($devices as $device) {
            $interestDevice = new UserInterestDevice();

            $interestDevice->setDevice($device);
            $interestDevice->setUserInterest($interest);

            $interest->addDevice($interestDevice);
        }

        $this->entityManager->persist($interest);
        $this->entityManager->flush();
    }
}

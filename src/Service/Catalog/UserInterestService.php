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

        $excludedMaterials = [];
        $includedMaterials = [];

        foreach ($materials as $material) {
            if (in_array($material->getId(), $excludedMaterialIds)) {
                $excludedMaterials[] = $material;
                break;
            }

            $includedMaterials[] = $material;
        }

        return $isExcluded ? $excludedMaterials : $includedMaterials;
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

        $excludedDevices = [];
        $includedDevices = [];

        foreach ($devices as $device) {
            if (in_array($device->getId(), $excludedDeviceIds)) {
                $excludedDevices[] = $device;
                break;
            } else {
                $hasExcludedComponent = false;

                foreach ($device->getCraftingComponents() as $componentRecord) {
                    if (in_array($componentRecord->getMaterial()->getId(), $excludedMaterialIds)) {
                        $hasExcludedComponent = true;
                        break;
                    }
                }

                if ($hasExcludedComponent) {
                    $excludedDevices[] = $device;
                    break;
                }
            }

            $includedDevices[] = $device;
        }

        return $isExcluded ? $excludedDevices : $includedDevices;
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

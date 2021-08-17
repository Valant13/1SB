<?php

namespace App\Entity\Catalog;

use App\Repository\Catalog\UserInterestDeviceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserInterestDeviceRepository::class)
 */
class UserInterestDevice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserInterest::class, inversedBy="devices")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $userInterest;

    /**
     * @ORM\ManyToOne(targetEntity=Device::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $device;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isExcluded;

    public function __construct()
    {
        $this->isExcluded = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserInterest(): ?UserInterest
    {
        return $this->userInterest;
    }

    public function setUserInterest(?UserInterest $userInterest): self
    {
        $this->userInterest = $userInterest;

        return $this;
    }

    public function getDevice(): ?Device
    {
        return $this->device;
    }

    public function setDevice(?Device $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getIsExcluded(): ?bool
    {
        return $this->isExcluded;
    }

    public function setIsExcluded(bool $isExcluded): self
    {
        $this->isExcluded = $isExcluded;

        return $this;
    }
}

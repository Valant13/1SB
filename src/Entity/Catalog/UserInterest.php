<?php

namespace App\Entity\Catalog;

use App\Entity\User\User;
use App\Repository\Catalog\UserInterestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserInterestRepository::class)
 */
class UserInterest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\OneToMany(
     *     targetEntity=UserInterestMaterial::class,
     *     mappedBy="userInterest",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     *     )
     */
    private $materials;

    /**
     * @ORM\OneToMany(
     *     targetEntity=UserInterestDevice::class,
     *     mappedBy="userInterest",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     *     )
     */
    private $devices;

    public function __construct()
    {
        $this->materials = new ArrayCollection();
        $this->devices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|UserInterestMaterial[]
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(UserInterestMaterial $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials[] = $material;
            $material->setUserInterest($this);
        }

        return $this;
    }

    public function removeMaterial(UserInterestMaterial $material): self
    {
        if ($this->materials->removeElement($material)) {
            // set the owning side to null (unless already changed)
            if ($material->getUserInterest() === $this) {
                $material->setUserInterest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserInterestDevice[]
     */
    public function getDevices(): Collection
    {
        return $this->devices;
    }

    public function addDevice(UserInterestDevice $device): self
    {
        if (!$this->devices->contains($device)) {
            $this->devices[] = $device;
            $device->setUserInterest($this);
        }

        return $this;
    }

    public function removeDevice(UserInterestDevice $device): self
    {
        if ($this->devices->removeElement($device)) {
            // set the owning side to null (unless already changed)
            if ($device->getUserInterest() === $this) {
                $device->setUserInterest(null);
            }
        }

        return $this;
    }

    /**
     * @return bool[]
     */
    public function getExcludedMaterialIds(): array
    {
        $excludedMaterialIds = [];
        foreach ($this->getMaterials() as $materialRecord) {
            if ($materialRecord->getIsExcluded()) {
                $excludedMaterialIds[] = $materialRecord->getMaterial()->getId();
            }
        }

        return $excludedMaterialIds;
    }

    /**
     * @return bool[]
     */
    public function getExcludedDeviceIds(): array
    {
        $excludedDeviceIds = [];
        foreach ($this->getDevices() as $deviceRecord) {
            if ($deviceRecord->getIsExcluded()) {
                $excludedDeviceIds[] = $deviceRecord->getDevice()->getId();
            }
        }

        return $excludedDeviceIds;
    }
}

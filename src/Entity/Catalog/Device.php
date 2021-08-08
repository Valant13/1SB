<?php

namespace App\Entity\Catalog;

use App\Repository\Catalog\DeviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DeviceRepository::class)
 */
class Device
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Product::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, unique=true)
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity=DeviceCraftingExperience::class, mappedBy="device", orphanRemoval=true)
     */
    private $craftingExperience;

    /**
     * @ORM\OneToMany(targetEntity=DeviceCraftingComponent::class, mappedBy="device", orphanRemoval=true)
     */
    private $deviceCraftingComponents;

    public function __construct()
    {
        $this->craftingExperience = new ArrayCollection();
        $this->deviceCraftingComponents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection|DeviceCraftingExperience[]
     */
    public function getCraftingExperience(): Collection
    {
        return $this->craftingExperience;
    }

    public function addCraftingExperience(DeviceCraftingExperience $craftingExperience): self
    {
        if (!$this->craftingExperience->contains($craftingExperience)) {
            $this->craftingExperience[] = $craftingExperience;
            $craftingExperience->setDevice($this);
        }

        return $this;
    }

    public function removeCraftingExperience(DeviceCraftingExperience $craftingExperience): self
    {
        if ($this->craftingExperience->removeElement($craftingExperience)) {
            // set the owning side to null (unless already changed)
            if ($craftingExperience->getDevice() === $this) {
                $craftingExperience->setDevice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|DeviceCraftingComponent[]
     */
    public function getDeviceCraftingComponents(): Collection
    {
        return $this->deviceCraftingComponents;
    }

    public function addDeviceCraftingComponent(DeviceCraftingComponent $deviceCraftingComponent): self
    {
        if (!$this->deviceCraftingComponents->contains($deviceCraftingComponent)) {
            $this->deviceCraftingComponents[] = $deviceCraftingComponent;
            $deviceCraftingComponent->setDevice($this);
        }

        return $this;
    }

    public function removeDeviceCraftingComponent(DeviceCraftingComponent $deviceCraftingComponent): self
    {
        if ($this->deviceCraftingComponents->removeElement($deviceCraftingComponent)) {
            // set the owning side to null (unless already changed)
            if ($deviceCraftingComponent->getDevice() === $this) {
                $deviceCraftingComponent->setDevice(null);
            }
        }

        return $this;
    }
}

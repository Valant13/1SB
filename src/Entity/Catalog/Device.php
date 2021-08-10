<?php

namespace App\Entity\Catalog;

use App\Repository\Catalog\DeviceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DeviceRepository::class)
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", unique=true)
     * @Assert\Valid
     */
    private $product;

    /**
     * @ORM\OneToMany(
     *     targetEntity=DeviceCraftingExperience::class,
     *     mappedBy="device",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     *     )
     * @Assert\Valid
     */
    private $craftingExperience;

    /**
     * @ORM\OneToMany(
     *     targetEntity=DeviceCraftingComponent::class,
     *     mappedBy="device",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     *     )
     * @Assert\Valid
     */
    private $craftingComponents;

    public function __construct()
    {
        $this->craftingExperience = new ArrayCollection();
        $this->craftingComponents = new ArrayCollection();
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
    public function getCraftingComponents(): Collection
    {
        return $this->craftingComponents;
    }

    public function addCraftingComponent(DeviceCraftingComponent $craftingComponent): self
    {
        if (!$this->craftingComponents->contains($craftingComponent)) {
            $this->craftingComponents[] = $craftingComponent;
            $craftingComponent->setDevice($this);
        }

        return $this;
    }

    public function removeCraftingComponent(DeviceCraftingComponent $craftingComponent): self
    {
        if ($this->craftingComponents->removeElement($craftingComponent)) {
            // set the owning side to null (unless already changed)
            if ($craftingComponent->getDevice() === $this) {
                $craftingComponent->setDevice(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\PreFlush
     */
    public function removeEmptyCraftingExperience(): void
    {
        foreach ($this->getCraftingExperience() as $craftingExperience) {
            if (!$craftingExperience->getQty()) {
                $this->removeCraftingExperience($craftingExperience);
            }
        }
    }

    /**
     * @ORM\PreFlush
     */
    public function removeEmptyCraftingComponents(): void
    {
        foreach ($this->getCraftingComponents() as $craftingComponent) {
            if (!$craftingComponent->getQty()) {
                $this->removeCraftingComponent($craftingComponent);
            }
        }
    }
}

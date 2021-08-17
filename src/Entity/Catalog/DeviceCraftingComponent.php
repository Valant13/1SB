<?php

namespace App\Entity\Catalog;

use App\Repository\Catalog\DeviceCraftingComponentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DeviceCraftingComponentRepository::class)
 */
class DeviceCraftingComponent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Device::class, inversedBy="craftingComponents")
     * @ORM\JoinColumn(nullable=false)
     */
    private $device;

    /**
     * @ORM\ManyToOne(targetEntity=Material::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $material;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @Assert\PositiveOrZero(message="Crafting component qty should be positive or zero")
     */
    private $qty;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }
}

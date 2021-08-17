<?php

namespace App\Entity\Calculator;

use App\Entity\Catalog\Material;
use App\Repository\Calculator\UserInventoryMaterialRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserInventoryMaterialRepository::class)
 */
class UserInventoryMaterial
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserInventory::class, inversedBy="materials")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $userInventory;

    /**
     * @ORM\ManyToOne(targetEntity=Material::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $material;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @Assert\PositiveOrZero (message="Inventory qty should be positive or zero")
     */
    private $qty;

    public function __construct()
    {
        $this->qty = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserInventory(): ?UserInventory
    {
        return $this->userInventory;
    }

    public function setUserInventory(?UserInventory $userInventory): self
    {
        $this->userInventory = $userInventory;

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

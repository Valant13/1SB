<?php

namespace App\Entity\Calculator;

use App\Entity\User\User;
use App\Repository\Calculator\UserInventoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserInventoryRepository::class)
 */
class UserInventory
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
     *     targetEntity=UserInventoryMaterial::class,
     *     mappedBy="userInventory",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     *     )
     */
    private $materials;

    public function __construct()
    {
        $this->materials = new ArrayCollection();
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
     * @return Collection|UserInventoryMaterial[]
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(UserInventoryMaterial $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials[] = $material;
            $material->setUserInventory($this);
        }

        return $this;
    }

    public function removeMaterial(UserInventoryMaterial $material): self
    {
        if ($this->materials->removeElement($material)) {
            // set the owning side to null (unless already changed)
            if ($material->getUserInventory() === $this) {
                $material->setUserInventory(null);
            }
        }

        return $this;
    }

    /**
     * @return int[]
     */
    public function getMaterialQuantities(): array
    {
        $materialQuantities = [];
        foreach ($this->getMaterials() as $materialRecord) {
            $materialQuantities[$materialRecord->getMaterial()->getId()] = $materialRecord->getQty();
        }

        return $materialQuantities;
    }
}

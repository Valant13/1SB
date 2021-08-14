<?php

namespace App\Entity\Calculator;

use App\Entity\User\User;
use App\Repository\Calculator\UserMiningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserMiningRepository::class)
 */
class UserMining
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
     *     targetEntity=UserMiningMaterial::class,
     *     mappedBy="userMining",
     *     orphanRemoval=true,
     *     cascade={"persist", "remove"}
     *     )
     * @Assert\Valid
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
     * @return Collection|UserMiningMaterial[]
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(UserMiningMaterial $material): self
    {
        if (!$this->materials->contains($material)) {
            $this->materials[] = $material;
            $material->setUserMining($this);
        }

        return $this;
    }

    public function removeMaterial(UserMiningMaterial $material): self
    {
        if ($this->materials->removeElement($material)) {
            // set the owning side to null (unless already changed)
            if ($material->getUserMining() === $this) {
                $material->setUserMining(null);
            }
        }

        return $this;
    }

    /**
     * @return bool[]
     */
    public function getAcceptableMaterialIds(): array
    {
        $acceptableMaterialIds = [];
        foreach ($this->getMaterials() as $materialRecord) {
            if ($materialRecord->getIsAcceptable()) {
                $acceptableMaterialIds[] = $materialRecord->getMaterial()->getId();
            }
        }

        return $acceptableMaterialIds;
    }
}

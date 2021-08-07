<?php

namespace App\Entity;

use App\Repository\UserMiningMaterialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserMiningMaterialRepository::class)
 */
class UserMiningMaterial
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Material::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $material;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAcceptable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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

    public function getIsAcceptable(): ?bool
    {
        return $this->isAcceptable;
    }

    public function setIsAcceptable(bool $isAcceptable): self
    {
        $this->isAcceptable = $isAcceptable;

        return $this;
    }
}

<?php

namespace App\Entity\Calculator;

use App\Entity\Catalog\Material;
use App\Repository\Calculator\UserMiningMaterialRepository;
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
     * @ORM\ManyToOne(targetEntity=UserMining::class, inversedBy="materials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userMining;

    /**
     * @ORM\ManyToOne(targetEntity=Material::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $material;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAcceptable;

    public function __construct()
    {
        $this->isAcceptable = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserMining(): ?UserMining
    {
        return $this->userMining;
    }

    public function setUserMining(?UserMining $userMining): self
    {
        $this->userMining = $userMining;

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

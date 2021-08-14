<?php

namespace App\Entity\Catalog;

use App\Repository\Catalog\UserInterestMaterialRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserInterestMaterialRepository::class)
 */
class UserInterestMaterial
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserInterest::class, inversedBy="materials")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userInterest;

    /**
     * @ORM\ManyToOne(targetEntity=Material::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $material;

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

    public function getMaterial(): ?Material
    {
        return $this->material;
    }

    public function setMaterial(?Material $material): self
    {
        $this->material = $material;

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

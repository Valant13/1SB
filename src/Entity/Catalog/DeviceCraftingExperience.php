<?php

namespace App\Entity\Catalog;

use App\Repository\Catalog\DeviceCraftingExperienceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DeviceCraftingExperienceRepository::class)
 */
class DeviceCraftingExperience
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Device::class, inversedBy="craftingExperience")
     * @ORM\JoinColumn(nullable=false)
     */
    private $device;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true})
     * @Assert\PositiveOrZero(message="Crafting experience qty should be positive or zero")
     */
    private $qty;

    /**
     * @ORM\ManyToOne(targetEntity=ResearchPoint::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $researchPoint;

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

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getResearchPoint(): ?ResearchPoint
    {
        return $this->researchPoint;
    }

    public function setResearchPoint(?ResearchPoint $researchPoint): self
    {
        $this->researchPoint = $researchPoint;

        return $this;
    }
}

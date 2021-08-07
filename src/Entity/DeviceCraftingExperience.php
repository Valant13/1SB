<?php

namespace App\Entity;

use App\Repository\DeviceCraftingExperienceRepository;
use Doctrine\ORM\Mapping as ORM;

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

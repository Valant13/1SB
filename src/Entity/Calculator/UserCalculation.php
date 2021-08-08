<?php

namespace App\Entity\Calculator;

use App\Repository\Calculator\UserCalculationRepository;
use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserCalculationRepository::class)
 */
class UserCalculation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, unique=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maximizationParam;

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

    public function getMaximizationParam(): ?string
    {
        return $this->maximizationParam;
    }

    public function setMaximizationParam(?string $maximizationParam): self
    {
        $this->maximizationParam = $maximizationParam;

        return $this;
    }
}

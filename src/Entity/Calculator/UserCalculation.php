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
    const CREDIT_CODE = 'credit';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", unique=true)
     */
    private $user;

    /**
     * @ORM\Column(name="maximization_param", type="string", length=255, nullable=true)
     */
    private $maximizationParamCode;

    public function __construct()
    {
        $this->maximizationParamCode = UserCalculation::CREDIT_CODE;
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

    public function getMaximizationParamCode(): ?string
    {
        return $this->maximizationParamCode;
    }

    public function setMaximizationParamCode(?string $maximizationParamCode): self
    {
        $this->maximizationParamCode = $maximizationParamCode;

        return $this;
    }
}

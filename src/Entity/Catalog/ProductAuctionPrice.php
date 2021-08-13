<?php

namespace App\Entity\Catalog;

use App\Entity\User\User;
use App\Repository\Catalog\ProductAuctionPriceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductAuctionPriceRepository::class)
 */
class ProductAuctionPrice
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true})
     * @Assert\Positive(message="Auction price should be positive")
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $modificationUser;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modificationTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(?int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getModificationUser(): ?User
    {
        return $this->modificationUser;
    }

    public function setModificationUser(?User $modificationUser): self
    {
        $this->modificationUser = $modificationUser;

        return $this;
    }

    public function getModificationTime(): ?\DateTimeInterface
    {
        return $this->modificationTime;
    }

    public function setModificationTime(?\DateTimeInterface $modificationTime): self
    {
        $this->modificationTime = $modificationTime;

        return $this;
    }
}

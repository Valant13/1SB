<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true, options={"unsigned":true})
     */
    private $marketplacePrice;

    /**
     * @ORM\OneToOne(targetEntity=ProductAuctionPrice::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, unique=true)
     */
    private $auctionPrice;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $imageUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $wikiPageUrl;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMarketplacePrice(): ?int
    {
        return $this->marketplacePrice;
    }

    public function setMarketplacePrice(?int $marketplacePrice): self
    {
        $this->marketplacePrice = $marketplacePrice;

        return $this;
    }

    public function getAuctionPrice(): ?ProductAuctionPrice
    {
        return $this->auctionPrice;
    }

    public function setAuctionPrice(ProductAuctionPrice $auctionPrice): self
    {
        $this->auctionPrice = $auctionPrice;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getWikiPageUrl(): ?string
    {
        return $this->wikiPageUrl;
    }

    public function setWikiPageUrl(?string $wikiPageUrl): self
    {
        $this->wikiPageUrl = $wikiPageUrl;

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

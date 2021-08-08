<?php

namespace App\ViewModel\Material;

class ListItem
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string|null
     */
    private $imageUrl;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var int|null
     */
    private $marketplacePrice;

    /**
     * @var string|null
     */
    private $modifiedString;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @param string|null $imageUrl
     */
    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getMarketplacePrice(): ?int
    {
        return $this->marketplacePrice;
    }

    /**
     * @param int|null $marketplacePrice
     */
    public function setMarketplacePrice(?int $marketplacePrice): void
    {
        $this->marketplacePrice = $marketplacePrice;
    }

    /**
     * @return string|null
     */
    public function getModifiedString(): ?string
    {
        return $this->modifiedString;
    }

    /**
     * @param string|null $modifiedString
     */
    public function setModifiedString(?string $modifiedString): void
    {
        $this->modifiedString = $modifiedString;
    }
}

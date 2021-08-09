<?php

namespace App\ViewModel\Device;

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
    private $wikiPageUrl;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $marketplacePrice;

    /**
     * @var string|null
     */
    private $modification;

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
    public function getWikiPageUrl(): ?string
    {
        return $this->wikiPageUrl;
    }

    /**
     * @param string|null $wikiPageUrl
     */
    public function setWikiPageUrl(?string $wikiPageUrl): void
    {
        $this->wikiPageUrl = $wikiPageUrl;
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
     * @return string|null
     */
    public function getMarketplacePrice(): ?string
    {
        return $this->marketplacePrice;
    }

    /**
     * @param string|null $marketplacePrice
     */
    public function setMarketplacePrice(?string $marketplacePrice): void
    {
        $this->marketplacePrice = $marketplacePrice;
    }

    /**
     * @return string|null
     */
    public function getModification(): ?string
    {
        return $this->modification;
    }

    /**
     * @param string|null $modification
     */
    public function setModification(?string $modification): void
    {
        $this->modification = $modification;
    }
}

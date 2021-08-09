<?php

namespace App\ViewModel\Material;

use App\Entity\Catalog\Material;
use App\ViewModel\AbstractViewModel;
use Symfony\Component\HttpFoundation\Request;

class Edit extends AbstractViewModel
{
    /**
     * @var int|null
     */
    private $id;

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
    private $imageUrl;

    /**
     * @var string|null
     */
    private $wikiPageUrl;

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->marketplacePrice = $request->request->getInt('marketplace-price') ?: null;
        $this->imageUrl = $request->request->get('image-url');
        $this->wikiPageUrl = $request->request->get('wiki-page-url');
    }

    /**
     * @param Material $material
     */
    public function fillFromMaterial(Material $material): void
    {
        $this->marketplacePrice = $material->getProduct()->getMarketplacePrice();
        $this->imageUrl = $material->getProduct()->getImageUrl();
        $this->wikiPageUrl = $material->getProduct()->getWikiPageUrl();
    }

    /**
     * @param Material $material
     */
    public function fillMaterial(Material $material): void
    {
        $material->getProduct()->setMarketplacePrice($this->marketplacePrice);
        $material->getProduct()->setImageUrl($this->imageUrl);
        $material->getProduct()->setWikiPageUrl($this->wikiPageUrl);
    }

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
}

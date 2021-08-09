<?php

namespace App\ViewModel\Device;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Product;
use App\Entity\Catalog\ProductAuctionPrice;
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
        $this->name = $request->request->get('name');
        $this->marketplacePrice = $request->request->getInt('marketplace-price') ?: null;
        $this->imageUrl = $request->request->get('image-url');
        $this->wikiPageUrl = $request->request->get('wiki-page-url');
    }

    /**
     * @param Device $device
     */
    public function fillFromDevice(Device $device): void
    {
        $this->name = $device->getProduct()->getName();
        $this->marketplacePrice = $device->getProduct()->getMarketplacePrice();
        $this->imageUrl = $device->getProduct()->getImageUrl();
        $this->wikiPageUrl = $device->getProduct()->getWikiPageUrl();
    }

    /**
     * @param Device $device
     */
    public function fillDevice(Device $device): void
    {
        if ($device->getProduct() === null) {
            $device->setProduct(new Product());
        }

        if ($device->getProduct()->getAuctionPrice() === null) {
            $device->getProduct()->setAuctionPrice(new ProductAuctionPrice());
        }

        $device->getProduct()->setName($this->name);
        $device->getProduct()->setMarketplacePrice($this->marketplacePrice);
        $device->getProduct()->setImageUrl($this->imageUrl);
        $device->getProduct()->setWikiPageUrl($this->wikiPageUrl);
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

<?php

namespace App\ViewModel\Device;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\ResearchPoint;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Device\Edit\CraftingComponentGrid;
use App\ViewModel\Device\Edit\CraftingExperienceGrid;
use App\ViewModel\Grid\Grid;
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
     * @var Grid
     */
    private $craftingExperienceGrid;

    /**
     * @var Grid
     */
    private $craftingComponentGrid;

    /**
     * @param ResearchPoint[] $researchPoints
     * @param Material[] $materials
     */
    public function __construct(array $researchPoints, array $materials)
    {
        $this->craftingExperienceGrid = new Grid(
            'crafting-experience-grid',
            new CraftingExperienceGrid(),
            $researchPoints
        );

        $this->craftingComponentGrid = new Grid(
            'crafting-component-grid',
            new CraftingComponentGrid(),
            $materials
        );
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->name = $request->request->get('name');
        $this->marketplacePrice = $request->request->getInt('marketplace-price') ?: null;
        $this->imageUrl = $request->request->get('image-url');
        $this->wikiPageUrl = $request->request->get('wiki-page-url');

        $this->craftingExperienceGrid->fillFromRequest($request);
        $this->craftingComponentGrid->fillFromRequest($request);
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

        $this->craftingExperienceGrid->fillFromModels($device->getCraftingExperience()->toArray());
        $this->craftingComponentGrid->fillFromModels($device->getCraftingComponents()->toArray());
    }

    /**
     * @param Device $device
     */
    public function fillDevice(Device $device): void
    {
        $device->getProduct()->setName($this->name);
        $device->getProduct()->setMarketplacePrice($this->marketplacePrice);
        $device->getProduct()->setImageUrl($this->imageUrl);
        $device->getProduct()->setWikiPageUrl($this->wikiPageUrl);

        $this->craftingExperienceGrid->fillModels($device->getCraftingExperience()->toArray(), $device);
        $this->craftingComponentGrid->fillModels($device->getCraftingComponents()->toArray(), $device);
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

    /**
     * @return Grid
     */
    public function getCraftingExperienceGrid(): Grid
    {
        return $this->craftingExperienceGrid;
    }

    /**
     * @param Grid $craftingExperienceGrid
     */
    public function setCraftingExperienceGrid(Grid $craftingExperienceGrid): void
    {
        $this->craftingExperienceGrid = $craftingExperienceGrid;
    }

    /**
     * @return Grid
     */
    public function getCraftingComponentGrid(): Grid
    {
        return $this->craftingComponentGrid;
    }

    /**
     * @param Grid $craftingComponentGrid
     */
    public function setCraftingComponentGrid(Grid $craftingComponentGrid): void
    {
        $this->craftingComponentGrid = $craftingComponentGrid;
    }
}

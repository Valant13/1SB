<?php

namespace App\ViewModel\Device;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\Grid;
use App\ViewModel\Grid\Row;
use App\ViewModel\Grid\Value\Field;
use App\ViewModel\Grid\Value\Image;
use App\ViewModel\Grid\Value\Text;
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
    private $craftingComponentGrid;

    /**
     * @param Material[] $materials
     */
    public function __construct(array $materials)
    {
        $this->craftingComponentGrid = new Grid();
        $this->craftingComponentGrid->setIdForJs('crafting-component-form');
        $this->craftingComponentGrid->addColumn((new Column())->setName('Image')->setWidth(15));
        $this->craftingComponentGrid->addColumn((new Column())->setName('Name'));
        $this->craftingComponentGrid->addColumn((new Column())->setName('Qty')->setWidth(20)
            ->setControlType(Column::CONTROL_TYPE_CLEAR));

        foreach ($materials as $material) {
            $product = $material->getProduct();

            $row = new Row();

            $image = (new Image())
                ->setHref($product->getImageUrl());

            $name = (new Text())
                ->setText($product->getName());

            $qty = (new Field())
                ->setValueType('number')
                ->setName('crafting-components[' . $material->getId() . ']');

            $row->setValues([$image, $name, $qty]);

            $this->craftingComponentGrid->setRow($material->getId(), $row);
        }
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

        foreach ($device->getCraftingComponents() as $craftingComponent) {
            $index = $craftingComponent->getMaterial()->getId();
            $row = $this->craftingComponentGrid->getRow($index);

            /** @var Field $qty */
            $qty = $row->getValues()[2];
            $qty->setValue($craftingComponent->getQty());
        }
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

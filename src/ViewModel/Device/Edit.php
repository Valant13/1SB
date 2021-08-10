<?php

namespace App\ViewModel\Device;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\DeviceCraftingComponent;
use App\Entity\Catalog\DeviceCraftingExperience;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\ResearchPoint;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\Grid;
use App\ViewModel\Grid\Row;
use App\ViewModel\Grid\Value\Field;
use App\ViewModel\Grid\Value\Html;
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
    private $craftingExperienceGrid;

    /**
     * @var Grid
     */
    private $craftingComponentGrid;

    /**
     * @var ResearchPoint[]
     */
    private $researchPoints = [];

    /**
     * @var Material[]
     */
    private $materials = [];

    /**
     * @param ResearchPoint[] $researchPoints
     * @param Material[] $materials
     */
    public function __construct(array $researchPoints, array $materials)
    {
        foreach ($researchPoints as $researchPoint) {
            $this->researchPoints[$researchPoint->getId()] = $researchPoint;
        }

        foreach ($materials as $material) {
            $this->materials[$material->getId()] = $material;
        }

        $this->craftingExperienceGrid = $this->buildCraftingExperienceGrid($this->researchPoints);
        $this->craftingComponentGrid = $this->buildCraftingComponentGrid($this->materials);
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

        $this->fillCraftingExperienceFromRequest($request);
        $this->fillCraftingComponentsFromRequest($request);
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

        $this->fillCraftingExperienceFromDevice($device);
        $this->fillCraftingComponentsFromDevice($device);
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

        $this->fillDeviceWithCraftingExperience($device);
        $this->fillDeviceWithCraftingComponents($device);
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

    /**
     * @param ResearchPoint[] $researchPoints
     * @return Grid
     */
    private function buildCraftingExperienceGrid(array $researchPoints): Grid
    {
        $grid = new Grid();
        $grid->setIdForJs('crafting-experience-form');

        $grid->addColumn((new Column())->setName('Name'));
        $grid->addColumn((new Column())->setName('Qty')->setWidth(20)
            ->setControlType(Column::CONTROL_TYPE_CLEAR));

        foreach ($researchPoints as $researchPoint) {
            $row = new Row();

            $name = (new Html())->setHtml($researchPoint->getName());
            $qty = (new Field())->setValueType('number')
                ->setName('crafting-experience[' . $researchPoint->getId() . ']');

            $row->setValues(['name' => $name, 'qty' => $qty]);

            $grid->setRow($researchPoint->getId(), $row);
        }

        return $grid;
    }

    /**
     * @param Material[] $materials
     * @return Grid
     */
    private function buildCraftingComponentGrid(array $materials): Grid
    {
        $grid = new Grid();
        $grid->setIdForJs('crafting-component-form');

        $grid->addColumn((new Column())->setName('Image')->setWidth(15));
        $grid->addColumn((new Column())->setName('Name'));
        $grid->addColumn((new Column())->setName('Qty')->setWidth(20)
            ->setControlType(Column::CONTROL_TYPE_CLEAR));

        foreach ($materials as $material) {
            $product = $material->getProduct();

            $row = new Row();

            $image = (new Image())->setHref($product->getImageUrl());
            $name = (new Text())->setText($product->getName());
            $qty = (new Field())->setValueType('number')
                ->setName('crafting-components[' . $material->getId() . ']');

            $row->setValues(['image' => $image, 'name' => $name, 'qty' => $qty]);

            $grid->setRow($material->getId(), $row);
        }

        return $grid;
    }

    /**
     * @param Request $request
     */
    private function fillCraftingExperienceFromRequest(Request $request): void
    {
        /** @var array $craftingExperience */
        $craftingExperience = $request->request->get('crafting-experience');
        if (is_array($craftingExperience)) {
            foreach ($craftingExperience as $index => $value) {
                if ($this->craftingExperienceGrid->hasRow($index)) {
                    $row = $this->craftingExperienceGrid->getRow($index);

                    /** @var Field $qty */
                    $qty = $row->getValue('qty');
                    $qty->setValue(((int)$value) ?: null);
                }
            }
        }
    }

    /**
     * @param Request $request
     */
    private function fillCraftingComponentsFromRequest(Request $request): void
    {
        /** @var array $craftingComponents */
        $craftingComponents = $request->request->get('crafting-components');
        if (is_array($craftingComponents)) {
            foreach ($craftingComponents as $index => $value) {
                if ($this->craftingComponentGrid->hasRow($index)) {
                    $row = $this->craftingComponentGrid->getRow($index);

                    /** @var Field $qty */
                    $qty = $row->getValue('qty');
                    $qty->setValue(((int)$value) ?: null);
                }
            }
        }
    }

    /**
     * @param Device $device
     */
    private function fillCraftingExperienceFromDevice(Device $device): void
    {
        foreach ($device->getCraftingExperience() as $researchPoint) {
            $index = $researchPoint->getResearchPoint()->getId();
            $row = $this->craftingExperienceGrid->getRow($index);

            /** @var Field $qty */
            $qty = $row->getValue('qty');
            $qty->setValue($researchPoint->getQty());
        }
    }

    /**
     * @param Device $device
     */
    private function fillCraftingComponentsFromDevice(Device $device): void
    {
        foreach ($device->getCraftingComponents() as $craftingComponent) {
            $index = $craftingComponent->getMaterial()->getId();
            $row = $this->craftingComponentGrid->getRow($index);

            /** @var Field $qty */
            $qty = $row->getValue('qty');
            $qty->setValue($craftingComponent->getQty());
        }
    }

    /**
     * @param Device $device
     */
    private function fillDeviceWithCraftingExperience(Device $device): void
    {
        foreach ($this->craftingExperienceGrid->getRows() as $index => $row) {
            $qty = (int)$row->getValue('qty')->getValue();

            $researchPointExists = false;
            foreach ($device->getCraftingExperience() as $researchPoint) {
                if ($researchPoint->getResearchPoint()->getId() === $index) {
                    $researchPoint->setQty($qty);
                    $researchPointExists= true;
                    break;
                }
            }

            if (!$researchPointExists && $qty) {
                $researchPoint = new DeviceCraftingExperience();
                $researchPoint->setResearchPoint($this->researchPoints[$index]);
                $researchPoint->setDevice($device);
                $researchPoint->setQty($qty);

                $device->addCraftingExperience($researchPoint);
            }
        }
    }

    /**
     * @param Device $device
     */
    private function fillDeviceWithCraftingComponents(Device $device): void
    {
        foreach ($this->craftingComponentGrid->getRows() as $index => $row) {
            $qty = (int)$row->getValue('qty')->getValue();

            $componentExists = false;
            foreach ($device->getCraftingComponents() as $craftingComponent) {
                if ($craftingComponent->getMaterial()->getId() === $index) {
                    $craftingComponent->setQty($qty);
                    $componentExists= true;
                    break;
                }
            }

            if (!$componentExists && $qty) {
                $craftingComponent = new DeviceCraftingComponent();
                $craftingComponent->setMaterial($this->materials[$index]);
                $craftingComponent->setDevice($device);
                $craftingComponent->setQty($qty);

                $device->addCraftingComponent($craftingComponent);
            }
        }
    }
}

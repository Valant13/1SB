<?php

namespace App\ViewModel\Calculator;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\ResearchPoint;
use App\Service\Calculator\Data\CraftingDeal;
use App\Service\Calculator\Data\DealDestination;
use App\Service\Calculator\Data\DealInterface;
use App\Service\Calculator\Data\DealSource;
use App\Service\Calculator\Data\DeviceDeal;
use App\Service\Calculator\Data\MaterialDeal;
use App\Service\Calculator\Data\StockDestination;
use App\Service\Calculator\Data\StockSource;
use App\ViewModel\Formatter;
use App\ViewModel\Grid\Cell\Html;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\Grid;
use App\ViewModel\Grid\Row;

class DealGrid extends Grid
{
    const SOURCE_TYPE_NAMES = [
        StockSource::TYPE_INVENTORY => 'Inventory',
        StockSource::TYPE_MINING => 'Mining',
        StockSource::TYPE_AUCTION => 'Auction'
    ];

    const DESTINATION_TYPE_NAMES = [
        StockDestination::TYPE_MARKETPLACE => 'Marketplace',
        StockDestination::TYPE_AUCTION => 'Auction'
    ];

    /**
     * @var Material[]
     */
    private $indexedMaterials;

    /**
     * @var Device[]
     */
    private $indexedDevices;

    /**
     * @var ResearchPoint[]
     */
    private $indexedResearchPoints;

    /**
     * @param string $idForJs
     * @param Material[] $materials
     * @param Device[] $devices
     * @param ResearchPoint[] $researchPoints
     */
    public function __construct(string $idForJs, array $materials, array $devices, array $researchPoints)
    {
        parent::__construct($idForJs);

        $this->indexedMaterials = $this->getIndexedMaterials($materials);
        $this->indexedDevices = $this->getIndexedDevices($devices);
        $this->indexedResearchPoints = $this->getIndexedResearchPoints($researchPoints);

        $this->setColumns([
            'number' => (new Column())->setName('Number'),
            'get' => (new Column())->setName('Get'),
            'material' => (new Column())->setName('Material'),
            'craft' => (new Column())->setName('Craft'),
            'device' => (new Column())->setName('Device'),
            'sell' => (new Column())->setName('Sell'),
            'profit' => (new Column())->setName('Profit')
        ]);
    }

    /**
     * @param DealInterface[] $deals
     */
    public function fillForInventory(array $deals): void
    {
        $this->fillFromDeals($deals);

        $this->getColumns()['number']->setName('Step');
    }

    /**
     * @param DealInterface[] $deals
     */
    public function fillForMining(array $deals): void
    {
        $this->fillFromDeals($deals);

        $this->getColumns()['number']->setName('Option');
    }

    /**
     * @return bool
     */
    public function isStriped(): bool
    {
        return false;
    }

    /**
     * @param DealInterface[] $deals
     */
    private function fillFromDeals(array $deals): void
    {
        $rows = [];
        foreach ($deals as $index => $deal) {
            $rows = array_merge($rows, $this->createRowsForDeal($deal, $index + 1));
        }

        $this->setRows($rows);
    }

    /**
     * @param DealInterface $deal
     * @param int $number
     * @return Row[]
     */
    public function createRowsForDeal(DealInterface $deal, int $number): array
    {
        if ($deal instanceof MaterialDeal) {
            return $this->createRowsForMaterialDeal($deal, $number);
        } elseif ($deal instanceof DeviceDeal) {
            return $this->createRowsForDeviceDeal($deal, $number);
        } elseif ($deal instanceof CraftingDeal) {
            return $this->createRowsForCraftingDeal($deal, $number);
        }

        return [];
    }

    /**
     * @param MaterialDeal $deal
     * @param int $number
     * @return Row[]
     */
    private function createRowsForMaterialDeal(MaterialDeal $deal, int $number): array
    {
        $row = new Row();

        $row->setCells([
            'number' => $this->createCellForNumber($number),
            'get' => $this->createCellForSource($deal->getSource()),
            'material' => $this->createCellForMaterial($deal->getMaterialId()),
            'craft' => $this->createCellForCrafting(),
            'device' => $this->createCellForDevice(),
            'sell' => $this->createCellForDestination($deal->getDestination()),
            'profit' => $this->createCellForProfit($deal->getProfit())
        ]);

        return [$row];
    }

    /**
     * @param DeviceDeal $deal
     * @param int $number
     * @return Row[]
     */
    private function createRowsForDeviceDeal(DeviceDeal $deal, int $number): array
    {
        $row = new Row();

        $row->setCells([
            'number' => $this->createCellForNumber($number),
            'get' => $this->createCellForSource($deal->getSource()),
            'material' => $this->createCellForMaterial(),
            'craft' => $this->createCellForCrafting(),
            'device' => $this->createCellForDevice($deal->getDeviceId()),
            'sell' => $this->createCellForDestination($deal->getDestination()),
            'profit' => $this->createCellForProfit($deal->getProfit())
        ]);

        return [$row];
    }

    /**
     * @param CraftingDeal $deal
     * @param int $number
     * @return Row[]
     */
    private function createRowsForCraftingDeal(CraftingDeal $deal, int $number): array
    {
        $rows = [];

        $componentsCount = count($deal->getComponents());

        $isFirst = true;
        foreach ($deal->getComponents() as $component) {
            $row = new Row();

            if ($isFirst) {
                $row->setCell('number', $this->createCellForNumber($number)
                    ->setRowspan($componentsCount));
            }

            $row->setCell('get', $this->createCellForSource($component->getSource()));
            $row->setCell('material', $this->createCellForMaterial($component->getMaterialId()));

            if ($isFirst) {
                $craftQty = $deal->getQty() !== null ? $deal->getQty() : 1;

                $row->setCell('craft', $this->createCellForCrafting($craftQty)
                    ->setRowspan($componentsCount));
                $row->setCell('device', $this->createCellForDevice($deal->getDeviceId())
                    ->setRowspan($componentsCount));
                $row->setCell('sell', $this->createCellForDestination($deal->getDestination())
                    ->setRowspan($componentsCount));
                $row->setCell('profit', $this->createCellForProfit($deal->getProfit(), $deal->getExperience())
                    ->setRowspan($componentsCount));
            }

            $rows[] = $row;

            $isFirst = false;
        }

        return $rows;
    }

    /**
     * @param int $number
     * @return Html
     */
    private function createCellForNumber(int $number): Html
    {
        $cell = new Html();

        $cell->setHtml("<span>#$number</span>");

        return $cell;
    }

    /**
     * @param DealSource|null $source
     * @return Html
     */
    private function createCellForSource(?DealSource $source = null): Html
    {
        $cell = new Html();

        if ($source !== null) {
            $html = '';

            $type = $source->getType();
            $typeName = self::SOURCE_TYPE_NAMES[$type];
            $html .= "<span>$typeName</span><br>";

            $formattedQty = Formatter::formatQty($source->getQty());
            $html .= "<span>Qty: $formattedQty</span><br>";

            $price = $source->getPrice();
            if ($price > 0) {
                $formattedPrice = Formatter::formatPrice($price);
                $html .= "<span>Price: $formattedPrice</span><br>";
            }

            $cell->setHtml($html);
        }

        return $cell;
    }

    /**
     * @param DealDestination|null $destination
     * @return Html
     */
    private function createCellForDestination(?DealDestination $destination = null): Html
    {
        $cell = new Html();

        if ($destination !== null) {
            $html = '';

            $type = $destination->getType();
            $typeName = self::DESTINATION_TYPE_NAMES[$type];
            $html .= "<span>$typeName</span><br>";

            $formattedQty = Formatter::formatQty($destination->getQty());
            $html .= "<span>Qty: $formattedQty</span><br>";

            $price = $destination->getPrice();
            if ($price > 0) {
                $formattedPrice = Formatter::formatPrice($price);
                $html .= "<span>Price: $formattedPrice</span><br>";
            }

            $cell->setHtml($html);
        }

        return $cell;
    }

    /**
     * @param int|null $qty
     * @return Html
     */
    private function createCellForCrafting(?int $qty = null): Html
    {
        $cell = new Html();

        if ($qty !== null) {
            $cell->setHtml("<span>Qty: $qty</span>");
        }

        return $cell;
    }

    /**
     * @param int $profit
     * @param int[] $experience
     * @return Html
     */
    private function createCellForProfit(int $profit, array $experience = []): Html
    {
        $cell = new Html();

        $formattedProfit = Formatter::formatPrice($profit);
        $html = "<span>Credit: $formattedProfit</span><br>";

        foreach ($experience as $code => $qty) {
            $name = $this->indexedResearchPoints[$code]->getName();
            $html .= "<span>$name: $qty</span><br>";
        }

        $cell->setHtml($html);

        return $cell;
    }

    /**
     * @param int|null $materialId
     * @return Html
     */
    private function createCellForMaterial(?int $materialId = null): Html
    {
        $cell = new Html();

        if ($materialId !== null) {
            $cell->setHtml($this->indexedMaterials[$materialId]->getProduct()->getName());
        }

        return $cell;
    }

    /**
     * @param int|null $deviceId
     * @return Html
     */
    private function createCellForDevice(?int $deviceId = null): Html
    {
        $cell = new Html();

        if ($deviceId !== null) {
            $cell->setHtml($this->indexedDevices[$deviceId]->getProduct()->getName());
        }

        return $cell;
    }

    /**
     * @param Material[] $materials
     * @return Material[]
     */
    private function getIndexedMaterials(array $materials): array
    {
        $indexedMaterials = [];
        foreach ($materials as $material) {
            $indexedMaterials[$material->getId()] = $material;
        }

        return $indexedMaterials;
    }

    /**
     * @param Device[] $devices
     * @return Device[]
     */
    private function getIndexedDevices(array $devices): array
    {
        $indexedDevices = [];
        foreach ($devices as $device) {
            $indexedDevices[$device->getId()] = $device;
        }

        return $indexedDevices;
    }

    /**
     * @param ResearchPoint[] $researchPoints
     * @return ResearchPoint[]
     */
    private function getIndexedResearchPoints(array $researchPoints): array
    {
        $indexedResearchPoints = [];
        foreach ($researchPoints as $researchPoint) {
            $indexedResearchPoints[$researchPoint->getCode()] = $researchPoint;
        }

        return $indexedResearchPoints;
    }
}

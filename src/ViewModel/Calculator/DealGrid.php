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
            'number' => (new Column())->setName('Num.')->setWidth(40),
            'get' => (new Column())->setName('Get'),
            'material' => (new Column())->setName('Material')->setWidth(100),
            'device' => (new Column())->setName('Device')->setWidth(100),
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

        $this->getColumns()['number']->setName('Opt.');
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
        /** @var Row[] $rows */
        $rows = [];

        if ($deal instanceof MaterialDeal) {
            $rows = $this->createRowsForMaterialDeal($deal, $number);
        } elseif ($deal instanceof DeviceDeal) {
            $rows = $this->createRowsForDeviceDeal($deal, $number);
        } elseif ($deal instanceof CraftingDeal) {
            $rows = $this->createRowsForCraftingDeal($deal, $number);
        }

        if ($number % 2 === 0) {
            foreach ($rows as $row) {
                $row->setStyle('background-color: rgba(0,0,0,.05)');
            }
        }

        return $rows;
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
            'device' => $this->createCellForDevice(),
            'sell' => $this->createCellForDestination($deal->getDestination()),
            'profit' => $this->createCellForProfit($deal->getTotalProfit(), $deal->getProfitability())
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
            'device' => $this->createCellForDevice($deal->getDeviceId()),
            'sell' => $this->createCellForDestination($deal->getDestination()),
            'profit' => $this->createCellForProfit($deal->getTotalProfit(), $deal->getProfitability())
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
                $row->setCell('device', $this->createCellForDevice($deal->getDeviceId())
                    ->setRowspan($componentsCount));
                $row->setCell('sell', $this->createCellForDestination($deal->getDestination())
                    ->setRowspan($componentsCount));
                $row->setCell('profit', $this->createCellForProfit(
                    $deal->getTotalProfit(),
                    $deal->getProfitability(),
                    $deal->getTotalExperience()
                )->setRowspan($componentsCount));
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
            $html .= "<span class=\"font-weight-bold\">$typeName</span><br>";

            $formattedQty = Formatter::formatQty($source->getTotalQty());
            $html .= "<span class=\"text-nowrap\">Qty: <span class=\"font-weight-bold\">$formattedQty</span></span><br>";

            $price = $source->getTotalPrice();
            if ($price > 0) {
                $formattedPrice = Formatter::formatPrice($price);
                $html .= "<span class=\"font-weight-bold\">$formattedPrice</span><br>";
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
            $html .= "<span class=\"font-weight-bold\">$typeName</span><br>";

            $formattedQty = Formatter::formatQty($destination->getTotalQty());
            $html .= "<span class=\"text-nowrap\">Qty: <span class=\"font-weight-bold\">$formattedQty</span></span><br>";

            $price = $destination->getTotalPrice();
            if ($price > 0) {
                $formattedPrice = Formatter::formatPrice($price);
                $html .= "<span class=\"font-weight-bold\">$formattedPrice</span><br>";
            }

            $cell->setHtml($html);
        }

        return $cell;
    }

    /**
     * @param int $profit
     * @param float $profitability
     * @param int[] $experience
     * @return Html
     */
    private function createCellForProfit(int $profit, float $profitability, array $experience = []): Html
    {
        $cell = new Html();

        $formattedProfit = Formatter::formatPrice($profit);
        $html = "<span class=\"font-weight-bold\">$formattedProfit</span><br>";

        if (count($experience) > 0) {
            $html .= "<br>Experience:<br>";

            foreach ($experience as $code => $qty) {
                $icon = Formatter::getIcon($this->indexedResearchPoints[$code]->getIconUrl());

                $html .= "<span class=\"font-weight-bold\">$qty</span> $icon<br>";
            }
        }

        $formattedProfitability = Formatter::formatPercent($profitability);
        $html .= "<br><span>Profitability:</span><br>"
            . "<span class=\"font-weight-bold\">$formattedProfitability</span><br>";

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
            $product = $this->indexedMaterials[$materialId]->getProduct();
            $name = $product->getName();
            $imageUrl = $product->getImageUrl();

            $cell->setHtml(
                "<img src=\"$imageUrl\" class=\"img-fluid mb-1\">"
                . "<span class=\"font-weight-bold\">$name</span>"
            );
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
            $product = $this->indexedDevices[$deviceId]->getProduct();
            $name = $product->getName();
            $imageUrl = $product->getImageUrl();

            $cell->setHtml(
                "<img src=\"$imageUrl\" class=\"img-fluid mb-1\">"
                . "<span class=\"font-weight-bold\">$name</span>"
            );
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

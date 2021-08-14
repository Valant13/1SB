<?php

namespace App\Service\Calculator;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Service\Calculator\Data\CalculatorParams;
use App\Service\Calculator\Data\DeviceStockItem;
use App\Service\Calculator\Data\MaterialStockItem;
use App\Service\Calculator\Data\StockDestination;
use App\Service\Calculator\Data\StockSource;

class CalculatorParamsFactory
{
    const AUCTION_COMMISSION = 10;

    /**
     * @param Material[] $materials
     * @param Device[] $devices
     * @param int[] $inventoryQtys
     * @param int[] $miningAcceptableIds
     * @param string|null $maximizationParam
     * @return CalculatorParams
     */
    public function createParams(
        array $materials,
        array $devices,
        array $inventoryQtys,
        array $miningAcceptableIds,
        string $maximizationParam = null
    ): CalculatorParams {
        $params = new CalculatorParams();

        $params->setMaterialStockItems($this->createMaterialStockItems(
            $materials,
            $inventoryQtys,
            $miningAcceptableIds
        ));

        $params->setDeviceStockItems($this->createDeviceStockItems($devices));

        if ($maximizationParam !== null) {
            $params->setMaximizationParam($maximizationParam);
        }

        return $params;
    }

    /**
     * @param Material[] $materials
     * @param int[] $inventoryQtys
     * @param int[] $miningAcceptableIds
     * @return MaterialStockItem[]
     */
    private function createMaterialStockItems(array $materials, array $inventoryQtys, array $miningAcceptableIds): array
    {
        /** @var MaterialStockItem[] $materialItems */
        $materialItems = [];

        foreach ($materials as $material) {
            $materialId = $material->getId();

            $materialItem = new MaterialStockItem($materialId);

            $marketplacePrice = $material->getProduct()->getMarketplacePrice();
            $auctionPrice = $material->getProduct()->getAuctionPrice()->getValue();

            if (array_key_exists($materialId, $inventoryQtys) && $inventoryQtys[$materialId] > 0) {
                $stockSource = new StockSource(StockSource::TYPE_INVENTORY);
                $stockSource->setQty($inventoryQtys[$materialId]);
                $materialItem->getSources()[] = $stockSource;
            }

            if ($auctionPrice !== null) {
                $stockSource = new StockSource(StockSource::TYPE_AUCTION);
                $stockSource->setPrice($auctionPrice);
                $materialItem->getSources()[] = $stockSource;
            }

            if (in_array($materialId, $miningAcceptableIds)) {
                $stockSource = new StockSource(StockSource::TYPE_MINING);
                $materialItem->getSources()[] = $stockSource;
            }

            if ($marketplacePrice !== null) {
                $stockDestination = new StockDestination(StockDestination::TYPE_MARKETPLACE);
                $stockDestination->setPrice($marketplacePrice);
                $materialItem->getDestinations()[] = $stockDestination;
            }

            if ($auctionPrice !== null) {
                $stockDestination = new StockDestination(StockDestination::TYPE_AUCTION);
                $stockDestination->setPrice($this->getPriceExcludingCommission(
                    $auctionPrice,
                    self::AUCTION_COMMISSION
                ));
                $materialItem->getDestinations()[] = $stockDestination;
            }

            $materialItems[] = $materialItem;
        }

        return $materialItems;
    }

    /**
     * @param Device[] $devices
     * @return DeviceStockItem[]
     */
    private function createDeviceStockItems(array $devices): array
    {
        /** @var DeviceStockItem[] $deviceItems */
        $deviceItems = [];

        foreach ($devices as $device) {
            $deviceId = $device->getId();

            $deviceItem = new DeviceStockItem(
                $deviceId,
                $device->getCraftingExperienceQtys(),
                $device->getCraftingComponentsQtys()
            );

            $marketplacePrice = $device->getProduct()->getMarketplacePrice();
            $auctionPrice = $device->getProduct()->getAuctionPrice()->getValue();

            if ($auctionPrice !== null) {
                $stockSource = new StockSource(StockSource::TYPE_AUCTION);
                $stockSource->setPrice($auctionPrice);
                $deviceItem->getSources()[] = $stockSource;
            }

            if ($marketplacePrice !== null) {
                $stockDestination = new StockDestination(StockDestination::TYPE_MARKETPLACE);
                $stockDestination->setPrice($marketplacePrice);
                $deviceItem->getDestinations()[] = $stockDestination;
            }

            if ($auctionPrice !== null) {
                $stockDestination = new StockDestination(StockDestination::TYPE_AUCTION);
                $stockDestination->setPrice($this->getPriceExcludingCommission(
                    $auctionPrice,
                    self::AUCTION_COMMISSION
                ));
                $deviceItem->getDestinations()[] = $stockDestination;
            }

            $deviceItems[] = $deviceItem;
        }

        return $deviceItems;
    }

    /**
     * @param int $price
     * @param int $commission
     * @return int
     */
    private function getPriceExcludingCommission(int $price, int $commission): int
    {
        return $price / 100 * (100 - $commission);
    }
}

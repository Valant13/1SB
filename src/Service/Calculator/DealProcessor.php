<?php

namespace App\Service\Calculator;

use App\Service\Calculator\Data\CalculatorParams;
use App\Service\Calculator\Data\CraftingDeal;
use App\Service\Calculator\Data\CraftingDealComponent;
use App\Service\Calculator\Data\DealDestination;
use App\Service\Calculator\Data\DealInterface;
use App\Service\Calculator\Data\DealSource;
use App\Service\Calculator\Data\DeviceDeal;
use App\Service\Calculator\Data\DeviceStockItem;
use App\Service\Calculator\Data\MaterialDeal;
use App\Service\Calculator\Data\MaterialStockItem;
use App\Service\Calculator\Data\StockDestination;
use App\Service\Calculator\Data\StockSource;

class DealProcessor
{
    const CREDIT_PARAM = 'credit';

    /**
     * @param DealInterface[] $deals
     * @param bool $isProfitable
     */
    public function filterDealsByProfit(array &$deals, bool $isProfitable): void
    {
        $profitableDeals = [];
        $unprofitableDeals = [];

        foreach ($deals as $deal) {
            if ($deal->getProfit() >= 0) {
                $profitableDeals[] = $deal;
                break;
            }

            $unprofitableDeals[] = $deal;
        }

        $deals = $isProfitable ? $profitableDeals : $unprofitableDeals;
    }

    /**
     * @param DealInterface[] $deals
     * @param string $param
     * @param bool $asc
     */
    public function orderDealsByParam(
        array  &$deals,
        string $param = self::CREDIT_PARAM,
        bool   $asc = false
    ): void {
        if ($param === self::CREDIT_PARAM) {
            $orderingDeals = $deals;

            usort($deals, function ($a, $b) {
                return $a->getProfit() - $b->getProfit();
            });
        } else {
            $orderingDeals = [];
            foreach ($deals as $deal) {
                if ($deal instanceof CraftingDeal) {
                    $orderingDeals[] = $deal;
                }
            }

            usort($orderingDeals, function ($a, $b) use ($param) {
                $aParamValue = 0;
                $bParamValue = 0;

                if (array_key_exists($param, $a->getExperience())) {
                    $aParamValue = $a->getExperience()[$param];
                }

                if (array_key_exists($param, $b->getExperience())) {
                    $bParamValue = $b->getExperience()[$param];
                }

                $paramDifference = $aParamValue - $bParamValue;

                return $paramDifference ?: $a->getProfit() - $b->getProfit();
            });
        }

        if ($asc) {
            $deals = $orderingDeals;
        } else {
            $deals = array_reverse($orderingDeals);
        }
    }

    /**
     * @param MaterialStockItem[] $materialItems
     * @param string[] $allowedSourceTypes
     * @return MaterialDeal[]
     */
    public function getMaterialDeals(array $materialItems, array $allowedSourceTypes): array
    {
        $deals = [];

        foreach ($materialItems as $materialItem) {
            $stockSource = $this->getLowestPriceSource($materialItem->getSources(), $allowedSourceTypes);
            $stockDestination = $this->getHighestPriceDestination($materialItem->getDestinations());

            if ($stockSource === null || $stockDestination === null) {
                break;
            }

            $dealSource = new DealSource($stockSource->getType());
            $dealSource->setPrice($stockSource->getPrice());
            $dealSource->setQty(1);

            $dealDestination = new DealDestination($stockDestination->getType());
            $dealDestination->setPrice($stockDestination->getPrice());
            $dealDestination->setQty(1);

            $deal = new MaterialDeal($materialItem->getMaterialId(), $dealSource, $dealDestination);
            $deal->setProfit($dealDestination->getPrice() - $dealSource->getPrice());
            $deal->setQty($stockSource->getQty());

            $deals[] = $deal;
        }

        return $deals;
    }

    /**
     * @param DeviceStockItem[] $deviceItems
     * @param string[] $allowedSourceTypes
     * @return DeviceDeal[]
     */
    public function getDeviceDeals(array $deviceItems, array $allowedSourceTypes): array
    {
        $deals = [];

        foreach ($deviceItems as $deviceItem) {
            $stockSource = $this->getLowestPriceSource($deviceItem->getSources(), $allowedSourceTypes);
            $stockDestination = $this->getHighestPriceDestination($deviceItem->getDestinations());

            if ($stockSource === null || $stockDestination === null) {
                break;
            }

            $dealSource = new DealSource($stockSource->getType());
            $dealSource->setPrice($stockSource->getPrice());
            $dealSource->setQty(1);

            $dealDestination = new DealDestination($stockDestination->getType());
            $dealDestination->setPrice($stockDestination->getPrice());
            $dealDestination->setQty(1);

            $deal = new DeviceDeal($deviceItem->getDeviceId(), $dealSource, $dealDestination);
            $deal->setProfit($dealDestination->getPrice() - $dealSource->getPrice());
            $deal->setQty($stockSource->getQty());

            $deals[] = $deal;
        }

        return $deals;
    }

    /**
     * @param MaterialStockItem[] $materialItems
     * @param DeviceStockItem[] $deviceItems
     * @param string[] $allowedSourceTypes
     * @return CraftingDeal[]
     */
    public function getCraftingDeals(array $materialItems, array $deviceItems, array $allowedSourceTypes): array
    {
        $deals = [];

        foreach ($deviceItems as $deviceItem) {
            $dealComponents = $this->getCraftingDealComponents($deviceItem, $materialItems, $allowedSourceTypes);
            $stockDestination = $this->getHighestPriceDestination($deviceItem->getDestinations());

            if ($dealComponents === null || $stockDestination === null) {
                break;
            }

            $dealDestination = new DealDestination($stockDestination->getType());
            $dealDestination->setPrice($stockDestination->getPrice());
            $dealDestination->setQty(1);

            $deal = new CraftingDeal(
                $deviceItem->getDeviceId(),
                $deviceItem->getCraftingExperience(),
                $dealComponents,
                $dealDestination
            );

            $profit = $dealDestination->getPrice();
            foreach ($dealComponents as $dealComponent) {
                $profit -= $dealComponent->getSource()->getPrice() * $dealComponent->getSource()->getQty();
            }

            $deal->setProfit($profit);
            $deal->setQty($this->getMaxQtyForCraftingDealComponents($dealComponents, $materialItems));

            $deals[] = $deal;
        }

        return $deals;
    }

    /**
     * @param DeviceStockItem $deviceItem
     * @param MaterialStockItem[] $materialItems
     * @param string[] $allowedSourceTypes
     * @return CraftingDealComponent[]|null
     */
    private function getCraftingDealComponents(
        DeviceStockItem $deviceItem,
        array $materialItems,
        array $allowedSourceTypes
    ): ?array {
        $dealComponents = [];

        foreach ($deviceItem->getCraftingComponents() as $materialId => $materialQty) {
            if (!array_key_exists($materialId, $materialItems)) {
                return null;
            }

            $materialItem = $materialItems[$materialId];
            $stockSource = $this->getLowestPriceSource($materialItem->getSources(), $allowedSourceTypes, $materialQty);

            if ($stockSource === null) {
                return null;
            }

            $dealSource = new DealSource($stockSource->getType());
            $dealSource->setPrice($stockSource->getPrice());
            $dealSource->setQty($materialQty);

            $dealComponents[] = new CraftingDealComponent($materialId, $dealSource);
        }

        return $dealComponents;
    }

    /**
     * @param CraftingDealComponent[] $components
     * @param MaterialStockItem[] $materialItems
     * @return int|null
     */
    private function getMaxQtyForCraftingDealComponents(array $components, array $materialItems): ?int
    {
        $maxQty = PHP_INT_MAX;
        foreach ($components as $component) {
            $materialId = $component->getMaterialId();
            $stockSourceQty = $materialItems[$materialId]->getSources()[$component->getSource()->getType()]->getQty();

            if ($stockSourceQty === null) {
                return null;
            }

            $maxQty = min($maxQty, $stockSourceQty / $component->getSource()->getQty());
        }

        return $maxQty;
    }

    /**
     * @param StockSource[] $sources
     * @param string[] $allowedSourceTypes
     * @param float|null $requiredQty
     * @return StockSource|null
     */
    private function getLowestPriceSource(
        array $sources,
        array $allowedSourceTypes,
        ?float $requiredQty = null
    ): ?StockSource {
        $lowestSource = null;

        foreach ($sources as $source) {
            if (in_array($source->getType(), $allowedSourceTypes)) {
                if ($requiredQty !== null && !$source->isQtyInfinite() && $source->getQty() < $requiredQty) {
                    continue;
                }

                if ($lowestSource === null || $source->getPrice() < $lowestSource->getPrice()) {
                    $lowestSource = $source;
                }
            }
        }

        return $lowestSource;
    }

    /**
     * @param StockDestination[] $destinations
     * @return StockDestination|null
     */
    private function getHighestPriceDestination(array $destinations): ?StockDestination
    {
        $highestDestination = null;

        foreach ($destinations as $destination) {
            if ($highestDestination === null || $destination->getPrice() > $highestDestination->getPrice()) {
                $highestDestination = $destination;
            }
        }

        return $highestDestination;
    }
}

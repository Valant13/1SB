<?php

namespace App\Service\Calculator;

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
    public function filterDealsByProfitability(array &$deals, bool $isProfitable): void
    {
        $profitableDeals = [];
        $unprofitableDeals = [];

        foreach ($deals as $deal) {
            if ($deal->getProfitability() >= 0) {
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
                return $a->getProfitability() - $b->getProfitability();
            });
        } else {
            $orderingDeals = [];
            foreach ($deals as $deal) {
                if ($deal instanceof CraftingDeal) {
                    $orderingDeals[] = $deal;
                }
            }

            usort($orderingDeals, function ($a, $b) use ($param) {
                $aParam = 0;
                $bParam = 0;

                if (array_key_exists($param, $a->getExperience())) {
                    $aParam = $a->getTotalExperience()[$param];
                }

                if (array_key_exists($param, $b->getExperience())) {
                    $bParam = $b->getTotalExperience()[$param];
                }

                $aParamCost = $aParam / $a->getTotalCost();
                $bParamCost = $bParam / $b->getTotalCost();

                $paramCostDifference = $aParamCost - $bParamCost;

                return $paramCostDifference ?: $a->getProfitability() - $b->getProfitability();
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
            $stockSource = $this->getLowestStockSource($materialItem->getSources(), $allowedSourceTypes);
            $stockDestination = $this->getHighestStockDestination($materialItem->getDestinations());

            if ($stockSource === null || $stockDestination === null) {
                break;
            }

            $dealQty = $stockSource->getQty();
            $normalizedDealQty = $this->getNormalizedDealQty($dealQty);

            $dealSource = $this->createDealSource($stockSource, $normalizedDealQty);
            $dealDestination = $this->createDealDestination($stockDestination, $normalizedDealQty);

            $dealTotalCost = $this->getTotalCost($stockSource, $stockDestination, $normalizedDealQty);
            $dealTotalProfit = $this->getTotalProfit($stockSource, $stockDestination, $normalizedDealQty);
            $dealProfitability = $this->getProfitability($dealTotalCost, $dealTotalProfit);

            $deal = new MaterialDeal($materialItem->getMaterialId(), $dealSource, $dealDestination);
            $deal->setTotalCost($dealTotalCost);
            $deal->setTotalProfit($dealTotalProfit);
            $deal->setProfitability($dealProfitability);
            $deal->setQty($dealQty);

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
            $stockSource = $this->getLowestStockSource($deviceItem->getSources(), $allowedSourceTypes);
            $stockDestination = $this->getHighestStockDestination($deviceItem->getDestinations());

            if ($stockSource === null || $stockDestination === null) {
                break;
            }

            $dealQty = $stockSource->getQty();
            $normalizedDealQty = $this->getNormalizedDealQty($dealQty);

            $dealSource = $this->createDealSource($stockSource, $normalizedDealQty);
            $dealDestination = $this->createDealDestination($stockDestination, $normalizedDealQty);

            $dealTotalCost = $this->getTotalCost($stockSource, $stockDestination, $normalizedDealQty);
            $dealTotalProfit = $this->getTotalProfit($stockSource, $stockDestination, $normalizedDealQty);
            $dealProfitability = $this->getProfitability($dealTotalCost, $dealTotalProfit);

            $deal = new DeviceDeal($deviceItem->getDeviceId(), $dealSource, $dealDestination);
            $deal->setTotalCost($dealTotalCost);
            $deal->setTotalProfit($dealTotalProfit);
            $deal->setProfitability($dealProfitability);
            $deal->setQty($dealQty);

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
            $stockDestination = $this->getHighestStockDestination($deviceItem->getDestinations());

            if ($dealComponents === null || $stockDestination === null) {
                break;
            }

            $dealQty = $this->getCraftingDealQty($dealComponents, $materialItems);
            $normalizedDealQty = $this->getNormalizedDealQty($dealQty);

            foreach ($dealComponents as $dealComponent) {
                $dealSource = $dealComponent->getSource();
                $dealSource->setTotalPrice($dealSource->getTotalPrice() * $normalizedDealQty);
                $dealSource->setTotalQty($dealSource->getTotalQty() * $normalizedDealQty);

                $dealComponent->setTotalCost($dealComponent->getTotalCost() * $normalizedDealQty);
            }

            $dealTotalExperience = [];
            foreach ($deviceItem->getCraftingExperience() as $code => $value) {
                $dealTotalExperience[$code] = $value * $normalizedDealQty;
            }

            $dealDestination = $this->createDealDestination($stockDestination, $normalizedDealQty);

            $dealTotalCost = 0.0;
            $dealTotalProfit = $dealDestination->getTotalPrice();
            foreach ($dealComponents as $dealComponent) {
                $dealTotalCost += $dealComponent->getTotalCost();
                $dealTotalProfit -= $dealComponent->getSource()->getTotalPrice();
            }

            $dealProfitability = $this->getProfitability($dealTotalCost, $dealTotalProfit);

            $deal = new CraftingDeal(
                $deviceItem->getDeviceId(),
                $dealTotalExperience,
                $dealComponents,
                $dealDestination
            );

            $deal->setTotalCost($dealTotalCost);
            $deal->setTotalProfit($dealTotalProfit);
            $deal->setProfitability($dealProfitability);
            $deal->setQty($dealQty);

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

            $stockSource = $this->getLowestStockSource($materialItem->getSources(), $allowedSourceTypes, $materialQty);
            $stockDestination = $this->getHighestStockDestination($materialItem->getDestinations());

            if ($stockSource === null) {
                return null;
            }

            $dealSource = $this->createDealSource($stockSource, $materialQty);
            $dealTotalCost = $this->getTotalCost($stockSource, $stockDestination, $materialQty);

            $dealComponent = new CraftingDealComponent($materialId, $dealSource);
            $dealComponent->setTotalCost($dealTotalCost);

            $dealComponents[] = $dealComponent;
        }

        return $dealComponents;
    }

    /**
     * @param CraftingDealComponent[] $dealComponents
     * @param MaterialStockItem[] $materialItems
     * @return float|null
     */
    private function getCraftingDealQty(array $dealComponents, array $materialItems): ?float
    {
//        if (count($dealComponents) === 0) {
//            throw new \InvalidArgumentException('At least one component needed for crafting deal');
//        }

        $dealQty = null;
        foreach ($dealComponents as $component) {
            $materialId = $component->getMaterialId();
            $stockSource = $materialItems[$materialId]->getSources()[$component->getSource()->getType()];

            if ($stockSource->isQtyInfinite()) {
                continue;
            }

            $componentQty = $stockSource->getQty() / $component->getSource()->getTotalQty();
            $dealQty = $dealQty === null ? floor($componentQty) : min($dealQty, floor($componentQty));
        }

        return $dealQty;
    }

    /**
     * @param StockSource[] $sources
     * @param string[] $allowedSourceTypes
     * @param float|null $requiredQty
     * @return StockSource|null
     */
    private function getLowestStockSource(
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
    private function getHighestStockDestination(array $destinations): ?StockDestination
    {
        $highestDestination = null;

        foreach ($destinations as $destination) {
            if ($highestDestination === null || $destination->getPrice() > $highestDestination->getPrice()) {
                $highestDestination = $destination;
            }
        }

        return $highestDestination;
    }

    /**
     * @param StockSource $stockSource
     * @param float $totalQty
     * @return DealSource
     */
    private function createDealSource(StockSource $stockSource, float $totalQty): DealSource
    {
        $dealSource = new DealSource($stockSource->getType());
        $dealSource->setTotalPrice($stockSource->getPrice() * $totalQty);
        $dealSource->setTotalQty($totalQty);

        return $dealSource;
    }

    /**
     * @param StockDestination $stockDestination
     * @param float $totalQty
     * @return DealDestination
     */
    private function createDealDestination(StockDestination $stockDestination, float $totalQty): DealDestination
    {
        $dealDestination = new DealDestination($stockDestination->getType());
        $dealDestination->setTotalPrice($stockDestination->getPrice() * $totalQty);
        $dealDestination->setTotalQty($totalQty);

        return $dealDestination;
    }

    /**
     * @param StockSource $lowestStockSource
     * @param StockDestination|null $stockDestination
     * @param float $totalQty
     * @return float
     */
    private function getTotalCost(
        StockSource $lowestStockSource,
        ?StockDestination $stockDestination,
        float $totalQty
    ): float {
        if ($lowestStockSource->getPrice() > 0) {
            return $lowestStockSource->getPrice() * $totalQty;
        } elseif ($stockDestination !== null) {
            return $stockDestination->getPrice() * $totalQty;
        } else {
            return 0.0;
        }
    }

    /**
     * @param StockSource $lowestStockSource
     * @param StockDestination $stockDestination
     * @param float $totalQty
     * @return float
     */
    private function getTotalProfit(
        StockSource $lowestStockSource,
        StockDestination $stockDestination,
        float $totalQty
    ): float {
        return ($stockDestination->getPrice() - $lowestStockSource->getPrice()) * $totalQty;
    }

    /**
     * @param float $cost
     * @param float $profit
     * @return float
     */
    private function getProfitability(float $cost, float $profit): float
    {
        if ($cost == 0) {
            return 999.9;
        }

        return $profit / $cost;
    }

    /**
     * @param float|null $dealQty
     * @return float
     */
    private function getNormalizedDealQty(?float $dealQty): float
    {
        return $dealQty === null ? 1.0 : $dealQty;
    }
}

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
    const TOTAL_EXPERIENCE_PARAM = 'total_experience';

    /**
     * @param DealInterface[] $deals
     * @param string $param
     * @param bool $hasParam
     */
    public function filterDealsByParam(array &$deals, string $param = self::CREDIT_PARAM, bool $hasParam = true): void
    {
        if ($param === self::CREDIT_PARAM) {
            $this->filterDealsByProfit($deals, $hasParam);
        } else {
            $this->filterDealsByExperience($deals, $param, $hasParam);
        }
    }

    /**
     * Leave deal in list if it has at least one source of allowed type. Useful for crafting deals
     *
     * @param DealInterface[] $deals
     * @param string[] $allowedSourceTypes
     */
    public function filterDealsBySourceType(array &$deals, array $allowedSourceTypes): void
    {
        $filteredDeals = [];

        foreach ($deals as $deal) {
            if ($deal instanceof MaterialDeal || $deal instanceof DeviceDeal) {
                if (in_array($deal->getSource()->getType(), $allowedSourceTypes)) {
                    $filteredDeals[] = $deal;
                }
            } elseif ($deal instanceof CraftingDeal) {
                $hasAllowedSource = false;
                foreach ($deal->getComponents() as $component) {
                    if (in_array($component->getSource()->getType(), $allowedSourceTypes)) {
                        $hasAllowedSource = true;
                        break;
                    }
                }

                if ($hasAllowedSource) {
                    $filteredDeals[] = $deal;
                }
            }
        }

        $deals = $filteredDeals;
    }

    /**
     * @param DealInterface[] $deals
     * @param string $param
     * @param bool $asc
     */
    public function orderDealsByParam(array &$deals, string $param = self::CREDIT_PARAM, bool $asc = false): void
    {
        if ($param === self::CREDIT_PARAM) {
            usort($deals, function ($a, $b) {
                return $this->compareDealsByProfitability($a, $b);
            });
        } else {
            usort($deals, function ($a, $b) use ($param) {
                return $this->compareDealsByExperience($a, $b, $param);
            });
        }

        if (!$asc) {
            $deals = array_reverse($deals);
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
                continue;
            }

            $dealQty = $stockSource->getQty();
            $normalizedDealQty = $this->getNormalizedDealQty($dealQty);

            $dealSource = $this->createDealSource($stockSource, $normalizedDealQty);
            $dealDestination = $this->createDealDestination($stockDestination, $normalizedDealQty);

            $dealTotalCost = $this->getTotalCost($stockSource, $stockDestination, $normalizedDealQty);
            $dealTotalProfit = $this->getTotalProfit($stockSource, $stockDestination, $normalizedDealQty);
            $dealProfitability = $this->getProfitability($dealTotalCost, $dealDestination->getTotalPrice());

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
                continue;
            }

            $dealQty = $stockSource->getQty();
            $normalizedDealQty = $this->getNormalizedDealQty($dealQty);

            $dealSource = $this->createDealSource($stockSource, $normalizedDealQty);
            $dealDestination = $this->createDealDestination($stockDestination, $normalizedDealQty);

            $dealTotalCost = $this->getTotalCost($stockSource, $stockDestination, $normalizedDealQty);
            $dealTotalProfit = $this->getTotalProfit($stockSource, $stockDestination, $normalizedDealQty);
            $dealProfitability = $this->getProfitability($dealTotalCost, $dealDestination->getTotalPrice());

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
                continue;
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

            $dealProfitability = $this->getProfitability($dealTotalCost, $dealDestination->getTotalPrice());

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
     * @param MaterialStockItem[] $materialItems
     * @param DeviceStockItem[] $deviceItems
     * @param string[] $allowedSourceTypes
     * @return int[]
     */
    public function getDeviceCosts(array $materialItems, array $deviceItems, array $allowedSourceTypes): array
    {
        $costPrices = [];

        foreach ($deviceItems as $deviceItem) {
            $dealComponents = $this->getCraftingDealComponents($deviceItem, $materialItems, $allowedSourceTypes);

            if ($dealComponents === null) {
                continue;
            }

            $dealTotalCost = 0.0;
            foreach ($dealComponents as $dealComponent) {
                $dealTotalCost += $dealComponent->getTotalCost();
            }

            $costPrices[$deviceItem->getDeviceId()] = $dealTotalCost;
        }

        return $costPrices;
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
        if (empty($deviceItem->getCraftingComponents())) {
            return null;
        }

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
                if (!$source->isQtyInfinite() && $source->getQty() === 0.0) {
                    continue;
                }

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
     * @param StockDestination|null $highestStockDestination
     * @param float $totalQty
     * @return float
     */
    private function getTotalCost(
        StockSource $lowestStockSource,
        ?StockDestination $highestStockDestination,
        float $totalQty
    ): float {
        if ($lowestStockSource->getPrice() > 0) {
            return $lowestStockSource->getPrice() * $totalQty;
        } elseif ($highestStockDestination !== null) {
            return $highestStockDestination->getPrice() * $totalQty;
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
     * @param float $sellPrice
     * @return float
     */
    private function getProfitability(float $cost, float $sellPrice): float
    {
        if ($cost == 0) {
            return 999.9;
        }

        // This profit may not be equal to deal profit, since it is calculated from cost but not purchase price
        $productionProfit = $sellPrice - $cost;

        return $productionProfit / $cost;
    }

    /**
     * @param float|null $dealQty
     * @return float
     */
    private function getNormalizedDealQty(?float $dealQty): float
    {
        return $dealQty === null ? 1.0 : $dealQty;
    }

    /**
     * @param DealInterface[] $deals
     * @param bool $isProfitable
     */
    private function filterDealsByProfit(array &$deals, bool $isProfitable): void
    {
        $profitableDeals = [];
        $unprofitableDeals = [];

        foreach ($deals as $deal) {
            if ($deal->getTotalProfit() > 0) {
                $profitableDeals[] = $deal;
                continue;
            }

            $unprofitableDeals[] = $deal;
        }

        $deals = $isProfitable ? $profitableDeals : $unprofitableDeals;
    }

    /**
     * @param DealInterface[] $deals
     * @param string $researchPointCode
     * @param bool $hasExperience
     */
    private function filterDealsByExperience(array &$deals, string $researchPointCode, bool $hasExperience): void
    {
        $dealsHavingExperience = [];
        $dealsMissingExperience = [];

        foreach ($deals as $deal) {
            if ($deal instanceof CraftingDeal && $this->hasCraftingDealExperience($deal, $researchPointCode)) {
                $dealsHavingExperience[] = $deal;
                continue;
            }

            $dealsMissingExperience[] = $deal;
        }

        $deals = $hasExperience ? $dealsHavingExperience : $dealsMissingExperience;
    }

    /**
     * @param DealInterface $a
     * @param DealInterface $b
     * @return int
     */
    private function compareDealsByProfitability(DealInterface $a, DealInterface $b): int
    {
        $difference = $a->getProfitability() - $b->getProfitability();

        if ($difference !== 0.0) {
            return $difference > 0 ? 1 : -1;
        } else {
            return $a->getTotalProfit() > $b->getTotalProfit() ? 1 : -1;
        }
    }

    /**
     * @param DealInterface $a
     * @param DealInterface $b
     * @param string $researchPointCode
     * @return int
     */
    private function compareDealsByExperience(DealInterface $a, DealInterface $b, string $researchPointCode): int
    {
        $aTotalExperience = 0.0;
        $bTotalExperience = 0.0;

        if ($a instanceof CraftingDeal) {
            $aTotalExperience = $this->getCraftingDealExperience($a, $researchPointCode);
        }

        if ($b instanceof CraftingDeal) {
            $bTotalExperience = $this->getCraftingDealExperience($b, $researchPointCode);
        }

        if ($aTotalExperience === 0.0 && $bTotalExperience !== 0.0) {
            return -1;
        } elseif ($aTotalExperience !== 0.0 && $bTotalExperience === 0.0) {
            return 1;
        } elseif ($aTotalExperience === 0.0 && $bTotalExperience === 0.0) {
            return $this->compareDealsByProfitability($a, $b);
        }

        $aTotalProductionProfit = $a->getProfitability() * $a->getTotalCost();
        $bTotalProductionProfit = $b->getProfitability() * $b->getTotalCost();

        $difference = $aTotalProductionProfit / $aTotalExperience - $bTotalProductionProfit / $bTotalExperience;

        if ($difference !== 0.0) {
            return $difference > 0 ? 1 : -1;
        } else {
            return $this->compareDealsByProfitability($a, $b);
        }
    }

    /**
     * @param CraftingDeal $deal
     * @param string $researchPointCode
     * @return float
     */
    private function getCraftingDealExperience(CraftingDeal $deal, string $researchPointCode): float
    {
        if ($researchPointCode === self::TOTAL_EXPERIENCE_PARAM) {
            $experience = 0.0;

            foreach ($deal->getTotalExperience() as $value) {
                $experience += $value;
            }

            return $experience;
        } elseif (array_key_exists($researchPointCode, $deal->getTotalExperience())) {
            return $deal->getTotalExperience()[$researchPointCode];
        } else {
            return 0.0;
        }
    }

    /**
     * @param CraftingDeal $deal
     * @param string $researchPointCode
     * @return bool
     */
    private function hasCraftingDealExperience(CraftingDeal $deal, string $researchPointCode): bool
    {
        return $this->getCraftingDealExperience($deal, $researchPointCode) !== 0.0;
    }
}

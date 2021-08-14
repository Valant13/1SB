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

class CalculatorService
{
    /**
     * @param CalculatorParams $params
     * @param int $limit
     * @return DealInterface[]
     */
    public function calculateForInventory(CalculatorParams $params, int $limit = 1): array
    {
        $this->validateParams($params);
        $state = $this->getStateByParams($params);

        $resultDeals = [];

        for ($i = 0; $i < $limit; $i++) {
            $deals = $this->getPossibleDeals(
                $state->getMaterialStockItems(),
                $state->getDeviceStockItems(),
                [StockSource::TYPE_INVENTORY]
            );

            $this->filterDealsByProfit($deals, true);
            $this->orderDealsByParam($deals, $state->getMaximizationParam());

            if (empty($deals)) {
                break;
            }
            $deal = $deals[0];

            $this->subtractDealFromSource($deal, $state->getMaterialStockItems(), $state->getDeviceStockItems());

            $resultDeals[] = $deal;
        }

        return $resultDeals;
    }

    /**
     * @param CalculatorParams $params
     * @param int $limit
     * @return DealInterface[]
     */
    public function calculateForMining(CalculatorParams $params, int $limit = 1): array
    {
        $this->validateParams($params);
        $state = $this->getStateByParams($params);

        $deals = $this->getPossibleDeals(
            $state->getMaterialStockItems(),
            $state->getDeviceStockItems(),
            [StockSource::TYPE_MINING, StockSource::TYPE_AUCTION]
        );

        $this->filterDealsByProfit($deals, true);
        $this->orderDealsByParam($deals, $state->getMaximizationParam());

        return array_slice($deals, 0, $limit);
    }

    /**
     * @param CalculatorParams $params
     * @return int[]
     */
    public function calculateDeviceCostPrices(CalculatorParams $params): array
    {
        $this->validateParams($params);
        $state = $this->getStateByParams($params);

        $costPrices = [];

        $deals = $this->getCraftingDeals(
            $state->getMaterialStockItems(),
            $state->getDeviceStockItems(),
            [StockSource::TYPE_AUCTION]
        );

        foreach ($deals as $deal) {
            $costPrice = 0;
            foreach ($deal->getComponents() as $component) {
                $costPrice += $component->getSource()->getPrice();
            }

            $costPrices[$deal->getDeviceId()] = $costPrice;
        }

        return $costPrices;
    }

    /**
     * @param CalculatorParams $params
     * @throws \InvalidArgumentException
     */
    private function validateParams(CalculatorParams $params): void
    {
        // TODO: implement
    }

    /**
     * @param CalculatorParams $params
     * @return CalculatorParams
     */
    private function getStateByParams(CalculatorParams $params): CalculatorParams
    {
        $state = clone $params;

        $materialItems = [];
        foreach ($params->getMaterialStockItems() as $materialItem) {
            $sources = [];
            foreach ($materialItem->getSources() as $source) {
                $sources[$source->getType()] = $source;
            }
            $materialItem->setSources($sources);

            $destinations = [];
            foreach ($materialItem->getDestinations() as $destination) {
                $destinations[$destination->getType()] = $destination;
            }
            $materialItem->setDestinations($destinations);

            $materialItems[$materialItem->getMaterialId()] = $materialItem;
        }
        $state->setMaterialStockItems($materialItems);

        $deviceItems = [];
        foreach ($params->getDeviceStockItems() as $deviceItem) {
            $sources = [];
            foreach ($deviceItem->getSources() as $source) {
                $sources[$source->getType()] = $source;
            }
            $deviceItem->setSources($sources);

            $destinations = [];
            foreach ($deviceItem->getDestinations() as $destination) {
                $destinations[$destination->getType()] = $destination;
            }
            $deviceItem->setDestinations($destinations);

            $deviceItems[$deviceItem->getDeviceId()] = $deviceItem;
        }
        $state->setDeviceStockItems($deviceItems);

        return $state;
    }

    /**
     * @param MaterialStockItem[] $materialItems
     * @param DeviceStockItem[] $deviceItems
     * @param string[] $allowedSourceTypes
     * @return DealInterface[]
     */
    private function getPossibleDeals(array $materialItems, array $deviceItems, array $allowedSourceTypes): array
    {
        return array_merge(
            $this->getMaterialDeals($materialItems, $allowedSourceTypes),
            $this->getDeviceDeals($deviceItems, $allowedSourceTypes),
            $this->getCraftingDeals($materialItems, $deviceItems, $allowedSourceTypes)
        );
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
     * @param string $maximizationParam
     * @param bool $asc
     */
    private function orderDealsByParam(array &$deals, string $maximizationParam, bool $asc = false): void
    {
        if ($maximizationParam === CalculatorParams::CREDIT_PARAM) {
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

            usort($orderingDeals, function ($a, $b) use ($maximizationParam) {
                $aParamValue = 0;
                $bParamValue = 0;

                if (array_key_exists($maximizationParam, $a->getExperience())) {
                    $aParamValue = $a->getExperience()[$maximizationParam];
                }

                if (array_key_exists($maximizationParam, $b->getExperience())) {
                    $bParamValue = $b->getExperience()[$maximizationParam];
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
    private function getMaterialDeals(array $materialItems, array $allowedSourceTypes): array
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
    private function getDeviceDeals(array $deviceItems, array $allowedSourceTypes): array
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
    private function getCraftingDeals(array $materialItems, array $deviceItems, array $allowedSourceTypes): array
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
            if ($lowestSource === null) {
                $lowestSource = $source;
                break;
            }

            if (in_array($source->getType(), $allowedSourceTypes) && $source->getPrice() < $lowestSource->getPrice()) {
                if ($requiredQty === null) {
                    $lowestSource = $source;
                } else {
                    if ($source->isQtyInfinite() || $source->getQty() >= $requiredQty) {
                        $lowestSource = $source;
                    }
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
            if ($highestDestination === null) {
                $highestDestination = $destination;
                break;
            }

            if ($destination->getPrice() > $highestDestination->getPrice()) {
                $highestDestination = $destination;
            }
        }

        return $highestDestination;
    }

    /**
     * @param DealInterface $deal
     * @param MaterialStockItem[] $materialItems
     * @param DeviceStockItem[] $deviceItems
     */
    private function subtractDealFromSource(DealInterface $deal, array $materialItems, array $deviceItems): void
    {
        if ($deal->isQtyInfinite()) {
            return;
        }

        if ($deal instanceof MaterialDeal) {
            $source = $materialItems[$deal->getMaterialId()]->getSources()[$deal->getSource()->getType()];
            $source->setQty($source->getQty() - $deal->getSource()->getQty() * $deal->getQty());
        } elseif ($deal instanceof DeviceDeal) {
            $source = $deviceItems[$deal->getDeviceId()]->getSources()[$deal->getSource()->getType()];
            $source->setQty($source->getQty() - $deal->getSource()->getQty() * $deal->getQty());
        } elseif ($deal instanceof CraftingDeal) {
            foreach ($deal->getComponents() as $component) {
                $source = $materialItems[$component->getMaterialId()]->getSources()[$component->getSource()->getType()];
                $source->setQty($source->getQty() - $component->getSource()->getQty() * $deal->getQty());
            }
        }
    }
}

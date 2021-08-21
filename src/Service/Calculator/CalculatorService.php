<?php

namespace App\Service\Calculator;

use App\Service\Calculator\Data\CalculatorParams;
use App\Service\Calculator\Data\CraftingDeal;
use App\Service\Calculator\Data\DealInterface;
use App\Service\Calculator\Data\DeviceDeal;
use App\Service\Calculator\Data\DeviceStockItem;
use App\Service\Calculator\Data\MaterialDeal;
use App\Service\Calculator\Data\MaterialStockItem;
use App\Service\Calculator\Data\StockItemInterface;
use App\Service\Calculator\Data\StockSource;

class CalculatorService
{
    /**
     * @var DealProcessor
     */
    private $dealProcessor;

    /**
     * @param DealProcessor $dealProcessor
     */
    public function __construct(
        DealProcessor $dealProcessor
    ) {
        $this->dealProcessor = $dealProcessor;
    }

    /**
     * @param CalculatorParams $params
     * @param string $maximizationParam
     * @param int $limit
     * @return DealInterface[]
     */
    public function calculateForInventory(
        CalculatorParams $params,
        string $maximizationParam = DealProcessor::CREDIT_PARAM,
        int $limit = 1
    ): array {
        $this->validateParams($params);
        $state = $this->getStateByParams($params);

        $resultDeals = [];

        for ($i = 0; $i < $limit; $i++) {
            $deals = $this->getPossibleDeals(
                $state->getMaterialStockItems(),
                $state->getDeviceStockItems(),
                [StockSource::TYPE_INVENTORY]
            );

            $this->dealProcessor->filterDealsByParam($deals, $maximizationParam);
            $this->dealProcessor->orderDealsByParam($deals, $maximizationParam);

            if (empty($deals)) {
                break;
            }
            $deal = $deals[0];

            $this->subtractDealFromState($deal, $state);

            $resultDeals[] = $deal;
        }

        return $resultDeals;
    }

    /**
     * @param CalculatorParams $params
     * @param string $maximizationParam
     * @param int $limit
     * @return DealInterface[]
     */
    public function calculateForMining(
        CalculatorParams $params,
        string $maximizationParam = DealProcessor::CREDIT_PARAM,
        int $limit = 1
    ): array {
        $this->validateParams($params);
        $state = $this->getStateByParams($params);

        $materialDeals = $this->dealProcessor->getMaterialDeals(
            $state->getMaterialStockItems(),
            [StockSource::TYPE_MINING]
        );

        $craftingDeals = $this->dealProcessor->getCraftingDeals(
            $state->getMaterialStockItems(),
            $state->getDeviceStockItems(),
            [StockSource::TYPE_MINING, StockSource::TYPE_AUCTION]
        );

        $this->filterMiningCraftingDeals($craftingDeals);

        $deals = array_merge($materialDeals, $craftingDeals);

        $this->dealProcessor->filterDealsByParam($deals, $maximizationParam);
        $this->dealProcessor->orderDealsByParam($deals, $maximizationParam);

        return array_slice($deals, 0, $limit);
    }

    /**
     * @param CalculatorParams $params
     * @param string $maximizationParam
     * @param int $limit
     * @return DealInterface[]
     */
    public function calculateForTrade(
        CalculatorParams $params,
        string $maximizationParam = DealProcessor::CREDIT_PARAM,
        int $limit = 1
    ): array {
        $this->validateParams($params);
        $state = $this->getStateByParams($params);

        $deals = $this->getPossibleDeals(
            $state->getMaterialStockItems(),
            $state->getDeviceStockItems(),
            [StockSource::TYPE_AUCTION]
        );

        $this->dealProcessor->filterDealsByParam($deals, $maximizationParam);
        $this->dealProcessor->orderDealsByParam($deals, $maximizationParam);

        return array_slice($deals, 0, $limit);
    }

    /**
     * @param CalculatorParams $params
     * @return int[]
     */
    public function calculateDeviceCosts(CalculatorParams $params): array
    {
        $this->validateParams($params);
        $state = $this->getStateByParams($params);

        return $this->dealProcessor->getDeviceCosts(
            $state->getMaterialStockItems(),
            $state->getDeviceStockItems(),
            [StockSource::TYPE_AUCTION]
        );
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
            $this->indexStockItem($materialItem);

            $materialItems[$materialItem->getMaterialId()] = $materialItem;
        }

        $state->setMaterialStockItems($materialItems);

        $deviceItems = [];
        foreach ($params->getDeviceStockItems() as $deviceItem) {
            $this->indexStockItem($deviceItem);

            $deviceItems[$deviceItem->getDeviceId()] = $deviceItem;
        }

        $state->setDeviceStockItems($deviceItems);

        return $state;
    }

    /**
     * @param StockItemInterface $stockItem
     */
    private function indexStockItem(StockItemInterface $stockItem): void
    {
        $sources = [];
        foreach ($stockItem->getSources() as $source) {
            $sources[$source->getType()] = $source;
        }

        $stockItem->setSources($sources);

        $destinations = [];
        foreach ($stockItem->getDestinations() as $destination) {
            $destinations[$destination->getType()] = $destination;
        }

        $stockItem->setDestinations($destinations);
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
            $this->dealProcessor->getMaterialDeals($materialItems, $allowedSourceTypes),
            $this->dealProcessor->getDeviceDeals($deviceItems, $allowedSourceTypes),
            $this->dealProcessor->getCraftingDeals($materialItems, $deviceItems, $allowedSourceTypes)
        );
    }

    /**
     * @param CraftingDeal[] $deals
     */
    private function filterMiningCraftingDeals(array &$deals): void
    {
        $filteredDeals = [];

        foreach ($deals as $deal) {
            $hasMiningSource = false;
            foreach ($deal->getComponents() as $component) {
                if ($component->getSource()->getType() === StockSource::TYPE_MINING) {
                    $hasMiningSource = true;
                    break;
                }
            }

            if ($hasMiningSource) {
                $filteredDeals[] = $deal;
            }
        }

        $deals = $filteredDeals;
    }

    /**
     * @param DealInterface $deal
     * @param CalculatorParams $state
     */
    private function subtractDealFromState(DealInterface $deal, CalculatorParams $state): void
    {
        if ($deal->isQtyInfinite()) {
            return;
        }

        $materialItems = $state->getMaterialStockItems();
        $deviceItems = $state->getDeviceStockItems();

        if ($deal instanceof MaterialDeal) {
            $source = $materialItems[$deal->getMaterialId()]->getSources()[$deal->getSource()->getType()];
            $source->setQty($source->getQty() - $deal->getSource()->getTotalQty());
        } elseif ($deal instanceof DeviceDeal) {
            $source = $deviceItems[$deal->getDeviceId()]->getSources()[$deal->getSource()->getType()];
            $source->setQty($source->getQty() - $deal->getSource()->getTotalQty());
        } elseif ($deal instanceof CraftingDeal) {
            foreach ($deal->getComponents() as $component) {
                $source = $materialItems[$component->getMaterialId()]->getSources()[$component->getSource()->getType()];
                $source->setQty($source->getQty() - $component->getSource()->getTotalQty());
            }
        }
    }
}

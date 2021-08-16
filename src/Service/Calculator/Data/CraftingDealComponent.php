<?php

namespace App\Service\Calculator\Data;

class CraftingDealComponent
{
    /**
     * @var int
     */
    private $materialId;

    /**
     * @var DealSource
     */
    private $source;

    /**
     * @var float
     */
    private $totalCost;

    /**
     * @param int $materialId
     * @param DealSource $source
     */
    public function __construct(int $materialId, DealSource $source)
    {
        $this->materialId = $materialId;
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function getMaterialId(): int
    {
        return $this->materialId;
    }

    /**
     * @return DealSource
     */
    public function getSource(): DealSource
    {
        return $this->source;
    }

    /**
     * @return float
     */
    public function getTotalCost(): float
    {
        return $this->totalCost;
    }

    /**
     * @param float $totalCost
     */
    public function setTotalCost(float $totalCost): void
    {
        $this->totalCost = $totalCost;
    }
}

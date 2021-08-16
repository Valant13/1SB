<?php

namespace App\Service\Calculator\Data;

abstract class AbstractDeal implements DealInterface
{
    /**
     * @var DealDestination
     */
    private $destination;

    /**
     * @var float
     */
    private $totalCost = 0.0;

    /**
     * @var float
     */
    private $totalProfit = 0.0;

    /**
     * @var float
     */
    private $profitability = 0.0;

    /**
     * @var int|null
     */
    private $qty = null;

    /**
     * @param DealDestination $destination
     */
    public function __construct(DealDestination $destination)
    {
        $this->destination = $destination;
    }

    /**
     * @return DealDestination
     */
    public function getDestination(): DealDestination
    {
        return $this->destination;
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

    /**
     * @return float
     */
    public function getTotalProfit(): float
    {
        return $this->totalProfit;
    }

    /**
     * @param float $totalProfit
     */
    public function setTotalProfit(float $totalProfit): void
    {
        $this->totalProfit = $totalProfit;
    }

    /**
     * @return float
     */
    public function getProfitability(): float
    {
        return $this->profitability;
    }

    /**
     * @param float $profitability
     */
    public function setProfitability(float $profitability): void
    {
        $this->profitability = $profitability;
    }

    /**
     * @return int|null
     */
    public function getQty(): ?int
    {
        return $this->qty;
    }

    /**
     * @param int|null $qty
     */
    public function setQty(?int $qty): void
    {
        $this->qty = $qty;
    }

    /**
     * @return bool
     */
    public function isQtyInfinite(): bool
    {
        return $this->qty === null;
    }
}

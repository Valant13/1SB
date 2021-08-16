<?php

namespace App\Service\Calculator\Data;

interface DealInterface
{
    /**
     * @return DealDestination
     */
    function getDestination(): DealDestination;

    /**
     * @return float
     */
    function getTotalCost(): float;

    /**
     * @param float $totalCost
     */
    public function setTotalCost(float $totalCost): void;

    /**
     * @return float
     */
    function getTotalProfit(): float;

    /**
     * @param float $totalProfit
     */
    public function setTotalProfit(float $totalProfit): void;

    /**
     * @return float
     */
    function getProfitability(): float;

    /**
     * @param float $profitability
     */
    public function setProfitability(float $profitability): void;

    /**
     * @return int|null
     */
    function getQty(): ?int;

    /**
     * @param int|null $qty
     */
    function setQty(?int $qty): void;

    /**
     * @return bool
     */
    function isQtyInfinite(): bool;
}

<?php

namespace App\Service\Calculator\Data;

interface DealInterface
{
    /**
     * @return DealDestination
     */
    function getDestination(): DealDestination;

    /**
     * @return int
     */
    function getProfit(): int;

    /**
     * @param int $profit
     */
    public function setProfit(int $profit): void;

    /**
     * @return int
     */
    function getTotalProfit(): int;

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

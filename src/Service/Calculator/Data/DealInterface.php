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
     * @return int|null
     */
    function getQty(): ?int;

    /**
     * @return bool
     */
    function isQtyInfinite(): bool;
}

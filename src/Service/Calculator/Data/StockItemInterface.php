<?php

namespace App\Service\Calculator\Data;

interface StockItemInterface
{
    /**
     * @return StockSource[]
     */
    function getSources(): array;

    /**
     * @return StockDestination[]
     */
    function getDestinations(): array;
}

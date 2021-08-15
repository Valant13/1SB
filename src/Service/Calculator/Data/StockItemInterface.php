<?php

namespace App\Service\Calculator\Data;

interface StockItemInterface
{
    /**
     * @return StockSource[]
     */
    function getSources(): array;

    /**
     * @param StockSource[] $sources
     */
    public function setSources(array $sources): void;

    /**
     * @return StockDestination[]
     */
    function getDestinations(): array;

    /**
     * @param StockDestination[] $destinations
     */
    public function setDestinations(array $destinations): void;
}

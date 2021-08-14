<?php

namespace App\Service\Calculator\Data;

abstract class AbstractStockItem implements StockItemInterface
{
    /**
     * @var StockSource[]
     */
    private $sources = [];

    /**
     * @var StockDestination[]
     */
    private $destinations = [];

    /**
     *
     */
    public function __clone()
    {
        $sources = [];
        foreach ($this->sources as $source) {
            $sources[] = clone $source;
        }
        $this->sources = $sources;

        $destinations = [];
        foreach ($this->destinations as $destination) {
            $destinations[] = clone $destination;
        }
        $this->destinations = $destinations;
    }

    /**
     * @return StockSource[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    /**
     * @param StockSource[] $sources
     */
    public function setSources(array $sources): void
    {
        $this->sources = $sources;
    }

    /**
     * @return StockDestination[]
     */
    public function getDestinations(): array
    {
        return $this->destinations;
    }

    /**
     * @param StockDestination[] $destinations
     */
    public function setDestinations(array $destinations): void
    {
        $this->destinations = $destinations;
    }
}

<?php

namespace App\Service\Calculator\Data;

abstract class AbstractDeal implements DealInterface
{
    /**
     * @var DealDestination
     */
    private $destination;

    /**
     * @var int
     */
    private $profit = 0;

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
     * @return int
     */
    public function getProfit(): int
    {
        return $this->profit;
    }

    /**
     * @param int $profit
     */
    public function setProfit(int $profit): void
    {
        $this->profit = $profit;
    }

    /**
     * @return int
     */
    public function getTotalProfit(): int
    {
        return $this->qty === null ? $this->profit : $this->profit * $this->qty;
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
        $this->destination->setDealQty($qty);
    }

    /**
     * @return bool
     */
    public function isQtyInfinite(): bool
    {
        return $this->qty === null;
    }
}

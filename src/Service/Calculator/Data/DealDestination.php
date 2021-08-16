<?php

namespace App\Service\Calculator\Data;

class DealDestination
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var float
     */
    private $totalPrice = 0.0;

    /**
     * @var float
     */
    private $totalQty = 0.0;

    /**
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    /**
     * @param float $totalPrice
     */
    public function setTotalPrice(float $totalPrice): void
    {
        $this->totalPrice = $totalPrice;
    }

    /**
     * @return float
     */
    public function getTotalQty(): float
    {
        return $this->totalQty;
    }

    /**
     * @param float $totalQty
     */
    public function setTotalQty(float $totalQty): void
    {
        $this->totalQty = $totalQty;
    }
}

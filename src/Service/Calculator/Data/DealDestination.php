<?php

namespace App\Service\Calculator\Data;

class DealDestination
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $price = 0;

    /**
     * @var float
     */
    private $qty = 0.0;

    /**
     * @var int|null
     */
    private $dealQty = null;

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
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getTotalPrice(): int
    {
        return $this->dealQty === null ? $this->price : $this->price * $this->dealQty;
    }

    /**
     * @return float
     */
    public function getQty(): float
    {
        return $this->qty;
    }

    /**
     * @param float $qty
     */
    public function setQty(float $qty): void
    {
        $this->qty = $qty;
    }

    /**
     * @return float
     */
    public function getTotalQty(): float
    {
        return $this->dealQty === null ? $this->qty : $this->qty * $this->dealQty;
    }

    /**
     * @param int|null $dealQty
     */
    public function setDealQty(?int $dealQty): void
    {
        $this->dealQty = $dealQty;
    }
}

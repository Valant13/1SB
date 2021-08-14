<?php

namespace App\Service\Calculator\Data;

class StockSource
{
    const TYPE_INVENTORY = 'inventory';
    const TYPE_AUCTION = 'auction';
    const TYPE_MINING = 'mining';

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $price = 0;

    /**
     * @var float|null
     */
    private $qty = null;

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
     * @return float|null
     */
    public function getQty(): ?float
    {
        return $this->qty;
    }

    /**
     * @param float|null $qty
     */
    public function setQty(?float $qty): void
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

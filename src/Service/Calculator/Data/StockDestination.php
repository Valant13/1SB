<?php

namespace App\Service\Calculator\Data;

class StockDestination
{
    const TYPE_MARKETPLACE = 'marketplace';
    const TYPE_AUCTION = 'auction';

    /**
     * @var string
     */
    private $type;

    /**
     * @var int
     */
    private $price = 0;

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
}

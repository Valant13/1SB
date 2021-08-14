<?php

namespace App\Service\Calculator\Data;

class MaterialStockItem extends AbstractStockItem implements StockItemInterface
{
    /**
     * @var int
     */
    private $materialId;

    /**
     * @param int $materialId
     */
    public function __construct(int $materialId)
    {
        $this->materialId = $materialId;
    }

    /**
     * @return int
     */
    public function getMaterialId(): int
    {
        return $this->materialId;
    }
}

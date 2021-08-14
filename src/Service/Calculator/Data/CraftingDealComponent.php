<?php

namespace App\Service\Calculator\Data;

class CraftingDealComponent
{
    /**
     * @var int
     */
    private $materialId;

    /**
     * @var DealSource
     */
    private $source;

    /**
     * @param int $materialId
     * @param DealSource $source
     */
    public function __construct(int $materialId, DealSource $source)
    {
        $this->materialId = $materialId;
        $this->source = $source;
    }

    /**
     * @return int
     */
    public function getMaterialId(): int
    {
        return $this->materialId;
    }

    /**
     * @return DealSource
     */
    public function getSource(): DealSource
    {
        return $this->source;
    }
}

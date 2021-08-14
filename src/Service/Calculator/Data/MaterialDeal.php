<?php

namespace App\Service\Calculator\Data;

class MaterialDeal extends AbstractDeal implements DealInterface
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
     * @param DealDestination $destination
     */
    public function __construct(int $materialId, DealSource $source, DealDestination $destination)
    {
        parent::__construct($destination);

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

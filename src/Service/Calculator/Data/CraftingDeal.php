<?php

namespace App\Service\Calculator\Data;

class CraftingDeal extends AbstractDeal implements DealInterface
{
    /**
     * @var int
     */
    private $deviceId;

    /**
     * @var float[]
     */
    private $totalExperience;

    /**
     * @var CraftingDealComponent[]
     */
    private $components;

    /**
     * @param int $deviceId
     * @param float[] $totalExperience
     * @param CraftingDealComponent[] $components
     * @param DealDestination $destination
     */
    public function __construct(int $deviceId, array $totalExperience, array $components, DealDestination $destination)
    {
        parent::__construct($destination);

        $this->deviceId = $deviceId;
        $this->totalExperience = $totalExperience;
        $this->components = $components;
    }

    /**
     * @return int
     */
    public function getDeviceId(): int
    {
        return $this->deviceId;
    }

    /**
     * @return float[]
     */
    public function getTotalExperience(): array
    {
        return $this->totalExperience;
    }

    /**
     * @return CraftingDealComponent[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }
}

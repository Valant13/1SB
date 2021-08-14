<?php

namespace App\Service\Calculator\Data;

class CraftingDeal extends AbstractDeal implements DealInterface
{
    /**
     * @var int
     */
    private $deviceId;

    /**
     * @var int[]
     */
    private $experience;

    /**
     * @var CraftingDealComponent[]
     */
    private $components;

    /**
     * @param int $deviceId
     * @param int[] $experience
     * @param CraftingDealComponent[] $components
     * @param DealDestination $destination
     */
    public function __construct(int $deviceId, array $experience, array $components, DealDestination $destination)
    {
        parent::__construct($destination);

        $this->deviceId = $deviceId;
        $this->experience = $experience;
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
     * @return int[]
     */
    public function getExperience(): array
    {
        return $this->experience;
    }

    /**
     * @return CraftingDealComponent[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }
}

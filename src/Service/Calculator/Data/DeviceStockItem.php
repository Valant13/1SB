<?php

namespace App\Service\Calculator\Data;

class DeviceStockItem extends AbstractStockItem implements StockItemInterface
{
    /**
     * @var int
     */
    private $deviceId;

    /**
     * @var int[]
     */
    private $craftingExperience;

    /**
     * @var float[]
     */
    private $craftingComponents;

    /**
     * @param int $deviceId
     * @param int[] $craftingExperience
     * @param float[] $craftingComponents
     */
    public function __construct(int $deviceId, array $craftingExperience, array $craftingComponents)
    {
        $this->deviceId = $deviceId;
        $this->craftingExperience = $craftingExperience;
        $this->craftingComponents = $craftingComponents;
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
    public function getCraftingExperience(): array
    {
        return $this->craftingExperience;
    }

    /**
     * @return float[]
     */
    public function getCraftingComponents(): array
    {
        return $this->craftingComponents;
    }
}

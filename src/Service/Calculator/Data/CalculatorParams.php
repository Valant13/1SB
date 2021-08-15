<?php

namespace App\Service\Calculator\Data;

class CalculatorParams
{
    /**
     * @var MaterialStockItem[]
     */
    private $materialStockItems = [];

    /**
     * @var DeviceStockItem[]
     */
    private $deviceStockItems = [];

    /**
     *
     */
    public function __clone()
    {
        $materialItems = [];
        foreach ($this->materialStockItems as $materialItem) {
            $materialItems[] = clone $materialItem;
        }
        $this->materialStockItems = $materialItems;

        $deviceItems = [];
        foreach ($this->deviceStockItems as $deviceItem) {
            $deviceItems[] = clone $deviceItem;
        }
        $this->deviceStockItems = $deviceItems;
    }

    /**
     * @return MaterialStockItem[]
     */
    public function getMaterialStockItems(): array
    {
        return $this->materialStockItems;
    }

    /**
     * @param MaterialStockItem[] $materialStockItems
     */
    public function setMaterialStockItems(array $materialStockItems): void
    {
        $this->materialStockItems = $materialStockItems;
    }

    /**
     * @return DeviceStockItem[]
     */
    public function getDeviceStockItems(): array
    {
        return $this->deviceStockItems;
    }

    /**
     * @param DeviceStockItem[] $deviceStockItems
     */
    public function setDeviceStockItems(array $deviceStockItems): void
    {
        $this->deviceStockItems = $deviceStockItems;
    }
}

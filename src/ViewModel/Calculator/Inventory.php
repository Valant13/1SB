<?php

namespace App\ViewModel\Calculator;

use App\Entity\Calculator\UserCalculation;
use App\Entity\Calculator\UserInventory;
use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\ResearchPoint;
use App\Service\Calculator\Data\DealInterface;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Calculator\Inventory\InventoryMaterialGrid;
use App\ViewModel\Grid\Grid;
use Symfony\Component\HttpFoundation\Request;

class Inventory extends AbstractViewModel
{
    /**
     * @var MaximizationParamList
     */
    private $maximizationParamList;

    /**
     * @var DealGrid
     */
    private $dealGrid;

    /**
     * @var Grid
     */
    private $inventoryMaterialGrid;

    /**
     * @var bool
     */
    private $hasCalculationResult = false;

    /**
     * @param Material[] $materials
     * @param Device[] $devices
     * @param ResearchPoint[] $researchPoints
     */
    public function __construct(array $materials, array $devices, array $researchPoints)
    {
        $this->maximizationParamList = new MaximizationParamList($researchPoints);

        $this->dealGrid = new DealGrid('deal-grid', $materials, $devices, $researchPoints);

        $this->inventoryMaterialGrid = new Grid(
            'inventory-material-grid',
            new InventoryMaterialGrid(),
            $materials
        );
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->maximizationParamList->fillFromRequest($request);
        $this->addErrors($this->maximizationParamList->getErrors());

        $this->inventoryMaterialGrid->fillFromRequest($request);
    }

    /**
     * @param UserCalculation $userCalculation
     * @param UserInventory $userInventory
     */
    public function fillFromUser(UserCalculation $userCalculation, UserInventory $userInventory): void
    {
        $this->maximizationParamList->fillFromUserCalculation($userCalculation);
        $this->inventoryMaterialGrid->fillFromModels($userInventory->getMaterials()->toArray());
    }

    /**
     * @param DealInterface[] $deals
     */
    public function fillFromDeals(array $deals): void
    {
        $this->dealGrid->fillForInventory($deals);
        $this->hasCalculationResult = true;
    }

    /**
     * @param UserCalculation $userCalculation
     * @param UserInventory $userInventory
     */
    public function fillUser(UserCalculation $userCalculation, UserInventory $userInventory): void
    {
        $this->maximizationParamList->fillUserCalculation($userCalculation);
        $this->inventoryMaterialGrid->fillModels($userInventory->getMaterials()->toArray(), $userInventory);
    }

    /**
     * @return Grid
     */
    public function getInventoryMaterialGrid(): Grid
    {
        return $this->inventoryMaterialGrid;
    }

    /**
     * @param Grid $inventoryMaterialGrid
     */
    public function setInventoryMaterialGrid(Grid $inventoryMaterialGrid): void
    {
        $this->inventoryMaterialGrid = $inventoryMaterialGrid;
    }

    /**
     * @return MaximizationParamList
     */
    public function getMaximizationParamList(): MaximizationParamList
    {
        return $this->maximizationParamList;
    }

    /**
     * @param MaximizationParamList $maximizationParamList
     */
    public function setMaximizationParamList(MaximizationParamList $maximizationParamList): void
    {
        $this->maximizationParamList = $maximizationParamList;
    }

    /**
     * @return DealGrid
     */
    public function getDealGrid(): DealGrid
    {
        return $this->dealGrid;
    }

    /**
     * @param DealGrid $dealGrid
     */
    public function setDealGrid(DealGrid $dealGrid): void
    {
        $this->dealGrid = $dealGrid;
    }

    /**
     * @return bool
     */
    public function hasCalculationResult(): bool
    {
        return $this->hasCalculationResult;
    }

    /**
     * @param bool $hasCalculationResult
     */
    public function setHasCalculationResult(bool $hasCalculationResult): void
    {
        $this->hasCalculationResult = $hasCalculationResult;
    }
}

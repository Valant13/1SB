<?php

namespace App\ViewModel\Calculator;

use App\Entity\Calculator\UserCalculation;
use App\Entity\Calculator\UserInventory;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\ResearchPoint;
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
     * @var Grid
     */
    private $inventoryMaterialGrid;

    /**
     * @param ResearchPoint[] $researchPoints
     * @param Material[] $materials
     */
    public function __construct(array $researchPoints, array $materials)
    {
        $this->maximizationParamList = new MaximizationParamList($researchPoints);

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
        $this->errors = array_merge($this->errors, $this->maximizationParamList->getErrors());

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
}

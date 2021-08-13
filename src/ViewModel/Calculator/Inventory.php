<?php

namespace App\ViewModel\Calculator;

use App\Entity\Calculator\UserInventory;
use App\Entity\Catalog\Material;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Calculator\Inventory\InventoryMaterialGrid;
use App\ViewModel\Grid\Grid;
use Symfony\Component\HttpFoundation\Request;

class Inventory extends AbstractViewModel
{
    /**
     * @var Grid
     */
    private $inventoryMaterialGrid;

    /**
     * @param Material[] $materials
     */
    public function __construct(array $materials)
    {
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
        $this->inventoryMaterialGrid->fillFromRequest($request);
    }

    /**
     * @param UserInventory $userInventory
     */
    public function fillFromUser(UserInventory $userInventory): void
    {
        $this->inventoryMaterialGrid->fillFromModels($userInventory->getMaterials()->toArray());
    }

    /**
     * @param UserInventory $userInventory
     */
    public function fillUser(UserInventory $userInventory): void
    {
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
}

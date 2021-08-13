<?php

namespace App\ViewModel\Calculator;

use App\Entity\Calculator\UserMining;
use App\Entity\Catalog\Material;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Calculator\Mining\MiningMaterialGrid;
use App\ViewModel\Grid\Grid;
use Symfony\Component\HttpFoundation\Request;

class Mining extends AbstractViewModel
{
    /**
     * @var Grid
     */
    private $miningMaterialGrid;

    /**
     * @param Material[] $materials
     */
    public function __construct(array $materials)
    {
        $this->miningMaterialGrid = new Grid(
            'mining-material-grid',
            new MiningMaterialGrid(),
            $materials
        );
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->miningMaterialGrid->fillFromRequest($request);
    }

    /**
     * @param UserMining $userMining
     */
    public function fillFromUser(UserMining $userMining): void
    {
        $this->miningMaterialGrid->fillFromModels($userMining->getMaterials()->toArray());
    }

    /**
     * @param UserMining $userMining
     */
    public function fillUser(UserMining $userMining): void
    {
        $this->miningMaterialGrid->fillModels($userMining->getMaterials()->toArray(), $userMining);
    }

    /**
     * @return Grid
     */
    public function getMiningMaterialGrid(): Grid
    {
        return $this->miningMaterialGrid;
    }

    /**
     * @param Grid $miningMaterialGrid
     */
    public function setMiningMaterialGrid(Grid $miningMaterialGrid): void
    {
        $this->miningMaterialGrid = $miningMaterialGrid;
    }
}

<?php

namespace App\ViewModel\Calculator;

use App\Entity\Calculator\UserCalculation;
use App\Entity\Calculator\UserMining;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\ResearchPoint;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Calculator\Mining\MiningMaterialGrid;
use App\ViewModel\Grid\Grid;
use Symfony\Component\HttpFoundation\Request;

class Mining extends AbstractViewModel
{
    /**
     * @var MaximizationParamList
     */
    private $maximizationParamList;

    /**
     * @var Grid
     */
    private $miningMaterialGrid;

    /**
     * @param ResearchPoint[] $researchPoints
     * @param Material[] $materials
     */
    public function __construct(array $researchPoints, array $materials)
    {
        $this->maximizationParamList = new MaximizationParamList($researchPoints);

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
        $this->maximizationParamList->fillFromRequest($request);
        $this->errors = array_merge($this->errors, $this->maximizationParamList->getErrors());

        $this->miningMaterialGrid->fillFromRequest($request);
    }

    /**
     * @param UserCalculation $userCalculation
     * @param UserMining $userMining
     */
    public function fillFromUser(UserCalculation $userCalculation, UserMining $userMining): void
    {
        $this->maximizationParamList->fillFromUserCalculation($userCalculation);
        $this->miningMaterialGrid->fillFromModels($userMining->getMaterials()->toArray());
    }

    /**
     * @param UserCalculation $userCalculation
     * @param UserMining $userMining
     */
    public function fillUser(UserCalculation $userCalculation, UserMining $userMining): void
    {
        $this->maximizationParamList->fillUserCalculation($userCalculation);
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

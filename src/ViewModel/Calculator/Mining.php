<?php

namespace App\ViewModel\Calculator;

use App\Entity\Calculator\UserCalculation;
use App\Entity\Calculator\UserMining;
use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\ResearchPoint;
use App\Service\Calculator\Data\DealInterface;
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
     * @var bool
     */
    private $isAuctionSellAllowed = false;

    /**
     * @var DealGrid
     */
    private $dealGrid;

    /**
     * @var Grid
     */
    private $miningMaterialGrid;

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
        $this->addErrors($this->maximizationParamList->getErrors());

        $this->isAuctionSellAllowed = (bool)$request->request->get('auction-sell-allowed');

        $this->miningMaterialGrid->fillFromRequest($request);
    }

    /**
     * @param UserCalculation $userCalculation
     * @param UserMining $userMining
     */
    public function fillFromUser(UserCalculation $userCalculation, UserMining $userMining): void
    {
        $this->maximizationParamList->fillFromUserCalculation($userCalculation);
        $this->isAuctionSellAllowed = $userCalculation->getIsAuctionSellAllowed();
        $this->miningMaterialGrid->fillFromModels($userMining->getMaterials()->toArray());
    }

    /**
     * @param DealInterface[] $deals
     */
    public function fillFromDeals(array $deals): void
    {
        $this->dealGrid->fillForMining($deals);
        $this->hasCalculationResult = true;
    }

    /**
     * @param UserCalculation $userCalculation
     * @param UserMining $userMining
     */
    public function fillUser(UserCalculation $userCalculation, UserMining $userMining): void
    {
        $this->maximizationParamList->fillUserCalculation($userCalculation);
        $userCalculation->setIsAuctionSellAllowed($this->isAuctionSellAllowed);
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

    /**
     * @return bool
     */
    public function isAuctionSellAllowed(): bool
    {
        return $this->isAuctionSellAllowed;
    }

    /**
     * @param bool $isAuctionSellAllowed
     */
    public function setIsAuctionSellAllowed(bool $isAuctionSellAllowed): void
    {
        $this->isAuctionSellAllowed = $isAuctionSellAllowed;
    }
}

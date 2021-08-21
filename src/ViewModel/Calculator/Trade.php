<?php

namespace App\ViewModel\Calculator;

use App\Entity\Calculator\UserCalculation;
use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\ResearchPoint;
use App\Service\Calculator\Data\DealInterface;
use App\ViewModel\AbstractViewModel;
use Symfony\Component\HttpFoundation\Request;

class Trade extends AbstractViewModel
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
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->maximizationParamList->fillFromRequest($request);
        $this->addErrors($this->maximizationParamList->getErrors());
    }

    /**
     * @param UserCalculation $userCalculation
     */
    public function fillFromUser(UserCalculation $userCalculation): void
    {
        $this->maximizationParamList->fillFromUserCalculation($userCalculation);
    }

    /**
     * @param DealInterface[] $deals
     */
    public function fillFromDeals(array $deals): void
    {
        $this->dealGrid->fillForTrade($deals);
        $this->hasCalculationResult = true;
    }

    /**
     * @param UserCalculation $userCalculation
     */
    public function fillUser(UserCalculation $userCalculation): void
    {
        $this->maximizationParamList->fillUserCalculation($userCalculation);
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

<?php

namespace App\ViewModel\Auction;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\User\User;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Grid\Grid;
use App\ViewModel\Auction\Index\DevicePriceGrid;
use App\ViewModel\Auction\Index\MaterialPriceGrid;
use Symfony\Component\HttpFoundation\Request;

class Index extends AbstractViewModel
{
    /**
     * @var Grid
     */
    private $materialPriceGrid;

    /**
     * @var Grid
     */
    private $devicePriceGrid;

    /**
     * @param Material[] $materials
     * @param Device[] $devices
     */
    public function __construct(array $materials, array $devices)
    {
        $this->materialPriceGrid = new Grid(
            'material-price-grid',
            new MaterialPriceGrid(),
            $materials
        );

        $this->devicePriceGrid = new Grid(
            'device-price-grid',
            new DevicePriceGrid(),
            $devices
        );
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->materialPriceGrid->fillFromRequest($request);
        $this->devicePriceGrid->fillFromRequest($request);
    }

    /**
     * @param Material[] $materials
     */
    public function fillFromMaterials(array $materials): void
    {
        $this->materialPriceGrid->fillFromModels($materials);
    }

    /**
     * @param Device[] $devices
     */
    public function fillFromDevices(array $devices): void
    {
        $this->devicePriceGrid->fillFromModels($devices);
    }

    /**
     * @param Material[] $materials
     * @param User $user
     */
    public function fillMaterials(array $materials, User $user): void
    {
        $this->materialPriceGrid->fillModels($materials, $user);
    }

    /**
     * @param Device[] $devices
     * @param User $user
     */
    public function fillDevices(array $devices, User $user): void
    {
        $this->devicePriceGrid->fillModels($devices, $user);
    }

    /**
     * @return Grid
     */
    public function getMaterialPriceGrid(): Grid
    {
        return $this->materialPriceGrid;
    }

    /**
     * @param Grid $materialPriceGrid
     */
    public function setMaterialPriceGrid(Grid $materialPriceGrid): void
    {
        $this->materialPriceGrid = $materialPriceGrid;
    }

    /**
     * @return Grid
     */
    public function getDevicePriceGrid(): Grid
    {
        return $this->devicePriceGrid;
    }

    /**
     * @param Grid $devicePriceGrid
     */
    public function setDevicePriceGrid(Grid $devicePriceGrid): void
    {
        $this->devicePriceGrid = $devicePriceGrid;
    }
}

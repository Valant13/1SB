<?php

namespace App\ViewModel\Price;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\User\User;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Grid\Grid;
use App\ViewModel\Price\Edit\DeviceGrid;
use App\ViewModel\Price\Edit\MaterialGrid;
use Symfony\Component\HttpFoundation\Request;

class Edit extends AbstractViewModel
{
    /**
     * @var Grid
     */
    private $materialGrid;

    /**
     * @var Grid
     */
    private $deviceGrid;

    /**
     * @param Material[] $materials
     * @param Device[] $devices
     */
    public function __construct(array $materials, array $devices)
    {
        $this->materialGrid = new Grid(
            'material-grid',
            new MaterialGrid(),
            $materials
        );

        $this->deviceGrid = new Grid(
            'device-grid',
            new DeviceGrid(),
            $devices
        );
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->materialGrid->fillFromRequest($request);
        $this->deviceGrid->fillFromRequest($request);
    }

    /**
     * @param Material[] $materials
     */
    public function fillFromMaterials(array $materials): void
    {
        $this->materialGrid->fillFromModels($materials);
    }

    /**
     * @param Device[] $devices
     */
    public function fillFromDevices(array $devices): void
    {
        $this->deviceGrid->fillFromModels($devices);
    }

    /**
     * @param Material[] $materials
     * @param User $user
     */
    public function fillMaterials(array $materials, User $user): void
    {
        $this->materialGrid->fillModels($materials, $user);
    }

    /**
     * @param Device[] $devices
     * @param User $user
     */
    public function fillDevices(array $devices, User $user): void
    {
        $this->deviceGrid->fillModels($devices, $user);
    }

    /**
     * @return Grid
     */
    public function getMaterialGrid(): Grid
    {
        return $this->materialGrid;
    }

    /**
     * @param Grid $materialGrid
     */
    public function setMaterialGrid(Grid $materialGrid): void
    {
        $this->materialGrid = $materialGrid;
    }

    /**
     * @return Grid
     */
    public function getDeviceGrid(): Grid
    {
        return $this->deviceGrid;
    }

    /**
     * @param Grid $deviceGrid
     */
    public function setDeviceGrid(Grid $deviceGrid): void
    {
        $this->deviceGrid = $deviceGrid;
    }
}

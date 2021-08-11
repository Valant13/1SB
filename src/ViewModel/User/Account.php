<?php

namespace App\ViewModel\User;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\Material;
use App\Entity\Catalog\UserInterest;
use App\Entity\Catalog\UserInterestDevice;
use App\Entity\Catalog\UserInterestMaterial;
use App\Entity\User\User;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\Grid;
use App\ViewModel\Grid\Row;
use App\ViewModel\Grid\Value\Checkbox;
use App\ViewModel\Grid\Value\Image;
use App\ViewModel\Grid\Value\Text;
use Symfony\Component\HttpFoundation\Request;

class Account extends AbstractViewModel
{
    /**
     * @var string|null
     */
    private $nickname;

    /**
     * @var Grid
     */
    private $interestMaterialGrid;

    /**
     * @var Grid
     */
    private $interestDeviceGrid;

    /**
     * @param Material[] $materials
     * @param Device[] $devices
     */
    public function __construct(array $materials, array $devices)
    {
        $this->interestMaterialGrid = $this->buildInterestMaterialGrid($materials);
        $this->interestDeviceGrid = $this->buildInterestDeviceGrid($devices);
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        $this->nickname = $request->request->get('nickname');

        $this->fillInterestMaterialsFromRequest($request);
        $this->fillInterestDevicesFromRequest($request);
    }

    /**
     * @param User $user
     * @param UserInterest $userInterest
     */
    public function fillFromUser(User $user, UserInterest $userInterest): void
    {
        $this->nickname = $user->getNickname();

        $this->fillFromInterestMaterials($userInterest->getMaterials()->toArray());
        $this->fillFromInterestDevices($userInterest->getDevices()->toArray());
    }

    /**
     * @param User $user
     * @param UserInterest $userInterest
     */
    public function fillUser(User $user, UserInterest $userInterest): void
    {
        $user->setNickname($this->nickname);

        $this->fillInterestMaterials($userInterest->getMaterials()->toArray());
        $this->fillInterestDevices($userInterest->getDevices()->toArray());
    }

    /**
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @param string|null $nickname
     */
    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @return Grid
     */
    public function getInterestMaterialGrid(): Grid
    {
        return $this->interestMaterialGrid;
    }

    /**
     * @param Grid $interestMaterialGrid
     */
    public function setInterestMaterialGrid(Grid $interestMaterialGrid): void
    {
        $this->interestMaterialGrid = $interestMaterialGrid;
    }

    /**
     * @return Grid
     */
    public function getInterestDeviceGrid(): Grid
    {
        return $this->interestDeviceGrid;
    }

    /**
     * @param Grid $interestDeviceGrid
     */
    public function setInterestDeviceGrid(Grid $interestDeviceGrid): void
    {
        $this->interestDeviceGrid = $interestDeviceGrid;
    }

    /**
     * @param Material[] $materials
     * @return Grid
     */
    private function buildInterestMaterialGrid(array $materials): Grid
    {
        $grid = new Grid();
        $grid->setIdForJs('interest-material-form');

        $grid->addColumn((new Column())->setName('Image')->setWidth(15));
        $grid->addColumn((new Column())->setName('Name'));
        $grid->addColumn((new Column())->setName('Included')->setWidth(20)
            ->setControlType(Column::CONTROL_TYPE_SELECT_UNSELECT));

        foreach ($materials as $material) {
            $product = $material->getProduct();

            $row = new Row();

            $image = (new Image())->setHref($product->getImageUrl());
            $name = (new Text())->setText($product->getName());
            $included = (new Checkbox())->setName('interest-materials[' . $material->getId() . ']');

            $row->setValues(['image' => $image, 'name' => $name, 'included' => $included]);

            $grid->setRow($material->getId(), $row);
        }

        return $grid;
    }

    /**
     * @param Device[] $devices
     * @return Grid
     */
    private function buildInterestDeviceGrid(array $devices): Grid
    {
        $grid = new Grid();
        $grid->setIdForJs('interest-device-form');

        $grid->addColumn((new Column())->setName('Image')->setWidth(15));
        $grid->addColumn((new Column())->setName('Name'));
        $grid->addColumn((new Column())->setName('Included')->setWidth(20)
            ->setControlType(Column::CONTROL_TYPE_SELECT_UNSELECT));

        foreach ($devices as $device) {
            $product = $device->getProduct();

            $row = new Row();

            $image = (new Image())->setHref($product->getImageUrl());
            $name = (new Text())->setText($product->getName());
            $included = (new Checkbox())->setName('interest-devices[' . $device->getId() . ']');

            $row->setValues(['image' => $image, 'name' => $name, 'included' => $included]);

            $grid->setRow($device->getId(), $row);
        }

        return $grid;
    }

    /**
     * @param Request $request
     */
    private function fillInterestMaterialsFromRequest(Request $request): void
    {
        /** @var array $interestMaterials */
        $interestMaterials = $request->request->get('interest-materials');
        if (is_array($interestMaterials)) {
            foreach ($interestMaterials as $materialId => $isChecked) {
                if ($this->interestMaterialGrid->hasRow($materialId)) {
                    $row = $this->interestMaterialGrid->getRow($materialId);

                    /** @var Checkbox $included */
                    $included = $row->getValue('included');
                    $included->setIsChecked((bool)$isChecked);
                }
            }
        }
    }

    /**
     * @param Request $request
     */
    private function fillInterestDevicesFromRequest(Request $request): void
    {
        /** @var array $interestDevices */
        $interestDevices = $request->request->get('interest-devices');
        if (is_array($interestDevices)) {
            foreach ($interestDevices as $deviceId => $isChecked) {
                if ($this->interestDeviceGrid->hasRow($deviceId)) {
                    $row = $this->interestDeviceGrid->getRow($deviceId);

                    /** @var Checkbox $included */
                    $included = $row->getValue('included');
                    $included->setIsChecked((bool)$isChecked);
                }
            }
        }
    }

    /**
     * @param UserInterestMaterial[] $interestMaterials
     */
    private function fillFromInterestMaterials(array $interestMaterials): void
    {
        $indexedInterestMaterials = $this->getIndexedInterestMaterials($interestMaterials);

        foreach ($indexedInterestMaterials as $materialId => $interestMaterial) {
            $row = $this->interestMaterialGrid->getRow($materialId);

            /** @var Checkbox $included */
            $included = $row->getValue('included');
            $included->setIsChecked(!$interestMaterial->getIsExcluded());
        }
    }

    /**
     * @param UserInterestDevice[] $interestDevices
     */
    private function fillFromInterestDevices(array $interestDevices): void
    {
        $indexedInterestDevices = $this->getIndexedInterestDevices($interestDevices);

        foreach ($indexedInterestDevices as $deviceId => $interestDevice) {
            $row = $this->interestDeviceGrid->getRow($deviceId);

            /** @var Checkbox $included */
            $included = $row->getValue('included');
            $included->setIsChecked(!$interestDevice->getIsExcluded());
        }
    }

    /**
     * @param UserInterestMaterial[] $interestMaterials
     */
    private function fillInterestMaterials(array $interestMaterials): void
    {
        $indexedInterestMaterials = $this->getIndexedInterestMaterials($interestMaterials);

        foreach ($this->interestMaterialGrid->getRows() as $materialId => $row) {
            $isExcluded = !$row->getValue('included')->isChecked();

            $indexedInterestMaterials[$materialId]->setIsExcluded($isExcluded);
        }
    }

    /**
     * @param UserInterestDevice[] $interestDevices
     */
    private function fillInterestDevices(array $interestDevices): void
    {
        $indexedInterestDevices = $this->getIndexedInterestDevices($interestDevices);

        foreach ($this->interestDeviceGrid->getRows() as $deviceId => $row) {
            $isExcluded = !$row->getValue('included')->isChecked();

            $indexedInterestDevices[$deviceId]->setIsExcluded($isExcluded);
        }
    }

    /**
     * @param UserInterestMaterial[] $interestMaterials
     * @return UserInterestMaterial[]
     */
    private function getIndexedInterestMaterials(array $interestMaterials): array
    {
        $indexedInterestMaterials = [];
        foreach ($interestMaterials as $interestMaterial) {
            $materialId = $interestMaterial->getMaterial()->getId();
            $indexedInterestMaterials[$materialId] = $interestMaterial;
        }

        return $indexedInterestMaterials;
    }

    /**
     * @param UserInterestDevice[] $interestDevices
     * @return UserInterestDevice[]
     */
    private function getIndexedInterestDevices(array $interestDevices): array
    {
        $indexedInterestDevices = [];
        foreach ($interestDevices as $interestDevice) {
            $deviceId = $interestDevice->getDevice()->getId();
            $indexedInterestDevices[$deviceId] = $interestDevice;
        }

        return $indexedInterestDevices;
    }
}

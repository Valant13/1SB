<?php

namespace App\ViewModel\User\Account;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\UserInterest;
use App\Entity\Catalog\UserInterestDevice;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\GridBindingInterface;
use App\ViewModel\Grid\Row;
use App\ViewModel\Grid\Cell\Checkbox;
use App\ViewModel\Grid\Cell\Image;
use App\ViewModel\Grid\Cell\Text;

class InterestDeviceGrid implements GridBindingInterface
{
    /**
     * @param Device $prototype
     * @return int
     */
    function getPrototypeIndex($prototype): int
    {
        return $prototype->getId();
    }

    /**
     * @param UserInterestDevice $model
     * @return int
     */
    function getModelIndex($model): int
    {
        return $model->getDevice()->getId();
    }

    /**
     * @inheritDoc
     */
    function getRequestValueKeys(): array
    {
        return ['interest-devices'];
    }

    /**
     * @inheritDoc
     */
    function buildColumns(): array
    {
        $imageColumn = (new Column())->setName('Image')->setWidth(15);
        $nameColumn = (new Column())->setName('Name');
        $includedColumn = (new Column())->setName('Included')->setWidth(20)
            ->setControlType(Column::CONTROL_TYPE_SELECT_UNSELECT);

        return [
            'image' => $imageColumn,
            'name' => $nameColumn,
            'include' => $includedColumn
        ];
    }

    /**
     * @param int $index
     * @param Device $prototype
     * @return Row
     */
    function buildRow(int $index, $prototype): Row
    {
        $row = new Row();

        $product = $prototype->getProduct();

        $imageCell = (new Image())->setHref($product->getImageUrl());
        $nameCell = (new Text())->setText($product->getName());
        $includedCell = (new Checkbox())->setName("interest-devices[$index]");

        $row->setCells([
            'image' => $imageCell,
            'name' => $nameCell,
            'included' => $includedCell
        ]);

        return $row;
    }

    /**
     * @param int $index
     * @param Row $row
     * @param string[] $requestValues
     */
    function fillRowFromRequest(int $index, Row $row, array $requestValues): void
    {
        $isChecked = (bool)$requestValues['interest-devices'];

        $row->getCell('included')->setIsChecked($isChecked);
    }

    /**
     * @param int $index
     * @param Row $row
     * @param UserInterestDevice $model
     */
    function fillRowFromModel(int $index, Row $row, $model): void
    {
        $row->getCell('included')->setIsChecked(!$model->getIsExcluded());
    }

    /**
     * @param int $index
     * @param Row $row
     * @param Device $prototype
     * @param UserInterestDevice $model
     * @param UserInterest $parentModel
     */
    function fillModelFromRow(int $index, Row $row, $prototype, $model, $parentModel): void
    {
        $isExcluded = !$row->getCell('included')->isChecked();

        $model->setIsExcluded($isExcluded);
    }
}

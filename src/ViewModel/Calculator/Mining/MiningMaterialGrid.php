<?php

namespace App\ViewModel\Calculator\Mining;

use App\Config;
use App\Entity\Calculator\UserMining;
use App\Entity\Calculator\UserMiningMaterial;
use App\Entity\Catalog\Material;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\GridBindingInterface;
use App\ViewModel\Grid\Row;
use App\ViewModel\Grid\Cell\Checkbox;
use App\ViewModel\Grid\Cell\Image;
use App\ViewModel\Grid\Cell\Text;

class MiningMaterialGrid implements GridBindingInterface
{
    /**
     * @param Material $prototype
     * @return int
     */
    function getPrototypeIndex($prototype): int
    {
        return $prototype->getId();
    }

    /**
     * @param UserMiningMaterial $model
     * @return int
     */
    function getModelIndex($model): int
    {
        return $model->getMaterial()->getId();
    }

    /**
     * @inheritDoc
     */
    function getRequestValueKeys(): array
    {
        return ['mining-materials'];
    }

    /**
     * @inheritDoc
     */
    function buildColumns(): array
    {
        $imageColumn = (new Column())->setName('Image')->setWidth(Config::IMAGE_COLUMN_WIDTH);
        $nameColumn = (new Column())->setName('Name');
        $acceptableColumn = (new Column())->setName('Acceptable')->setWidth(Config::CHECKBOX_COLUMN_WIDTH)
            ->setControlType(Column::CONTROL_TYPE_SELECT_UNSELECT);

        return [
            'image' => $imageColumn,
            'name' => $nameColumn,
            'acceptable' => $acceptableColumn
        ];
    }

    /**
     * @param int $index
     * @param Material $prototype
     * @return Row
     */
    function buildRow(int $index, $prototype): Row
    {
        $row = new Row();

        $product = $prototype->getProduct();

        $imageCell = (new Image())->setSrc($product->getImageUrl());
        $nameCell = (new Text())->setText($product->getName());
        $acceptableCell = (new Checkbox())->setName("mining-materials[$index]");

        $row->setCells([
            'image' => $imageCell,
            'name' => $nameCell,
            'acceptable' => $acceptableCell
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
        $isChecked = (bool)$requestValues['mining-materials'];

        $row->getCell('acceptable')->setIsChecked($isChecked);
    }

    /**
     * @param int $index
     * @param Row $row
     * @param UserMiningMaterial $model
     */
    function fillRowFromModel(int $index, Row $row, $model): void
    {
        $row->getCell('acceptable')->setIsChecked($model->getIsAcceptable());
    }

    /**
     * @param int $index
     * @param Row $row
     * @param Material $prototype
     * @param UserMiningMaterial $model
     * @param UserMining $parentModel
     */
    function fillModelFromRow(int $index, Row $row, $prototype, $model, $parentModel): void
    {
        $isAcceptable = $row->getCell('acceptable')->isChecked();

        $model->setIsAcceptable($isAcceptable);
    }
}

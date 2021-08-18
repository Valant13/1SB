<?php

namespace App\ViewModel\Calculator\Inventory;

use App\Config;
use App\Entity\Calculator\UserInventoryMaterial;
use App\Entity\Calculator\UserMining;
use App\Entity\Calculator\UserMiningMaterial;
use App\Entity\Catalog\Material;
use App\ViewModel\Grid\Cell\Field;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\GridBindingInterface;
use App\ViewModel\Grid\Row;
use App\ViewModel\Grid\Cell\Image;
use App\ViewModel\Grid\Cell\Text;

class InventoryMaterialGrid implements GridBindingInterface
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
        return ['inventory-materials'];
    }

    /**
     * @inheritDoc
     */
    function buildColumns(): array
    {
        $imageColumn = (new Column())->setName('Image')->setWidth(Config::IMAGE_COLUMN_WIDTH);
        $nameColumn = (new Column())->setName('Name');
        $qtyColumn = (new Column())->setName('Qty (stacks)')->setWidth(Config::FIELD_COLUMN_WIDTH)
            ->setControlType(Column::CONTROL_TYPE_CLEAR);

        return [
            'image' => $imageColumn,
            'name' => $nameColumn,
            'qty' => $qtyColumn
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
        $qtyCell = (new Field())->setValueType('number')
            ->setName("inventory-materials[$index]");

        $row->setCells([
            'image' => $imageCell,
            'name' => $nameCell,
            'qty' => $qtyCell
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
        $value = (int)$requestValues['inventory-materials'] ?: null;

        $row->getCell('qty')->setValue($value);
    }

    /**
     * @param int $index
     * @param Row $row
     * @param UserInventoryMaterial $model
     */
    function fillRowFromModel(int $index, Row $row, $model): void
    {
        $row->getCell('qty')->setValue($model->getQty() ?: null);
    }

    /**
     * @param int $index
     * @param Row $row
     * @param Material $prototype
     * @param UserInventoryMaterial $model
     * @param UserMining $parentModel
     */
    function fillModelFromRow(int $index, Row $row, $prototype, $model, $parentModel): void
    {
        $qty = (int)$row->getCell('qty')->getValue();

        $model->setQty($qty);
    }
}

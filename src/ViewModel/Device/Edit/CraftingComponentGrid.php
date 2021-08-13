<?php

namespace App\ViewModel\Device\Edit;

use App\Entity\Catalog\Device;
use App\Entity\Catalog\DeviceCraftingComponent;
use App\Entity\Catalog\Material;
use App\ViewModel\Grid\Cell\Field;
use App\ViewModel\Grid\Cell\Image;
use App\ViewModel\Grid\Cell\Text;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\GridBindingInterface;
use App\ViewModel\Grid\Row;

class CraftingComponentGrid implements GridBindingInterface
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
     * @param DeviceCraftingComponent $model
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
        return ['crafting-components'];
    }

    /**
     * @inheritDoc
     */
    function buildColumns(): array
    {
        $imageColumn = (new Column())->setName('Image')->setWidth(15);
        $nameColumn = (new Column())->setName('Name');
        $qtyColumn = (new Column())->setName('Qty')->setWidth(20)
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

        $imageCell = (new Image())->setHref($product->getImageUrl());
        $nameCell = (new Text())->setText($product->getName());
        $qtyCell = (new Field())->setValueType('number')
            ->setName("crafting-components[$index]");

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
        $value = (int)$requestValues['crafting-components'] ?: null;

        $row->getCell('qty')->setValue($value);
    }

    /**
     * @param int $index
     * @param Row $row
     * @param DeviceCraftingComponent|null $model
     */
    function fillRowFromModel(int $index, Row $row, $model): void
    {
        if ($model !== null) {
            $row->getCell('qty')->setValue($model->getQty());
        }
    }

    /**
     * @param int $index
     * @param Row $row
     * @param Material $prototype
     * @param DeviceCraftingComponent|null $model
     * @param Device $parentModel
     */
    function fillModelFromRow(int $index, Row $row, $prototype, $model, $parentModel): void
    {
        $value = (int)$row->getCell('qty')->getValue();

        if ($model !== null) {
            $model->setQty($value);
        } elseif ($value) {
            $craftingComponent = new DeviceCraftingComponent();

            $craftingComponent->setMaterial($prototype);
            $craftingComponent->setDevice($parentModel);
            $craftingComponent->setQty($value);

            $parentModel->addCraftingComponent($craftingComponent);
        }
    }
}

<?php

namespace App\ViewModel\Device\Edit;

use App\Config;
use App\Entity\Catalog\Device;
use App\Entity\Catalog\DeviceCraftingExperience;
use App\Entity\Catalog\ResearchPoint;
use App\ViewModel\Grid\Cell\Field;
use App\ViewModel\Grid\Cell\Html;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\GridBindingInterface;
use App\ViewModel\Grid\Row;

class CraftingExperienceGrid implements GridBindingInterface
{
    /**
     * @param ResearchPoint $prototype
     * @return int
     */
    function getPrototypeIndex($prototype): int
    {
        return $prototype->getId();
    }

    /**
     * @param DeviceCraftingExperience $model
     * @return int
     */
    function getModelIndex($model): int
    {
        return $model->getResearchPoint()->getId();
    }

    /**
     * @inheritDoc
     */
    function getRequestValueKeys(): array
    {
        return ['crafting-experience'];
    }

    /**
     * @inheritDoc
     */
    function buildColumns(): array
    {
        $nameColumn = (new Column())->setName('Name');
        $qtyColumn = (new Column())->setName('Qty')->setWidth(Config::FIELD_COLUMN_WIDTH)
            ->setControlType(Column::CONTROL_TYPE_CLEAR);

        return [
            'name' => $nameColumn,
            'qty' => $qtyColumn
        ];
    }

    /**
     * @param int $index
     * @param ResearchPoint $prototype
     * @return Row
     */
    function buildRow(int $index, $prototype): Row
    {
        $row = new Row();

        $nameCell = (new Html())->setHtml($prototype->getName());
        $qtyCell = (new Field())->setValueType('number')
            ->setName("crafting-experience[$index]");

        $row->setCells([
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
        $value = (int)$requestValues['crafting-experience'] ?: null;

        $row->getCell('qty')->setValue($value);
    }

    /**
     * @param int $index
     * @param Row $row
     * @param DeviceCraftingExperience|null $model
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
     * @param ResearchPoint $prototype
     * @param DeviceCraftingExperience|null $model
     * @param Device $parentModel
     */
    function fillModelFromRow(int $index, Row $row, $prototype, $model, $parentModel): void
    {
        $value = (int)$row->getCell('qty')->getValue();

        if ($model !== null) {
            $model->setQty($value);
        } elseif ($value) {
            $craftingExperience = new DeviceCraftingExperience();

            $craftingExperience->setResearchPoint($prototype);
            $craftingExperience->setDevice($parentModel);
            $craftingExperience->setQty($value);

            $parentModel->addCraftingExperience($craftingExperience);
        }
    }
}

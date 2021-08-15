<?php

namespace App\ViewModel\Price\Index;

use App\Config;
use App\Entity\Catalog\Device;
use App\Entity\User\User;
use App\ViewModel\Formatter;
use App\ViewModel\Grid\Cell\Field;
use App\ViewModel\Grid\Cell\Html;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\GridBindingInterface;
use App\ViewModel\Grid\Row;
use App\ViewModel\Grid\Cell\Image;
use App\ViewModel\Grid\Cell\Text;

class DeviceGrid implements GridBindingInterface
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
     * @param Device $model
     * @return int
     */
    function getModelIndex($model): int
    {
        return $model->getId();
    }

    /**
     * @inheritDoc
     */
    function getRequestValueKeys(): array
    {
        return ['devices'];
    }

    /**
     * @inheritDoc
     */
    function buildColumns(): array
    {
        $imageColumn = (new Column())->setName('Image')->setWidth(Config::IMAGE_COLUMN_WIDTH);
        $nameColumn = (new Column())->setName('Name');
        $modifiedColumn = (new Column())->setName('Price modified')->setWidth(Config::MODIFICATION_COLUMN_WIDTH);
        $auctionPriceColumn = (new Column())->setName('Auction price')->setWidth(Config::FIELD_COLUMN_WIDTH)
            ->setControlType(Column::CONTROL_TYPE_CLEAR);

        return [
            'image' => $imageColumn,
            'name' => $nameColumn,
            'modified' => $modifiedColumn,
            'auction_price' => $auctionPriceColumn
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
        $modifiedCell = new Html();
        $auctionPriceCell = (new Field())->setValueType('number')
            ->setName("devices[$index]");

        $row->setCells([
            'image' => $imageCell,
            'name' => $nameCell,
            'modified' => $modifiedCell,
            'auction_price' => $auctionPriceCell
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
        $value = $requestValues['devices'];

        if ($value !== null) {
            $row->getCell('auction_price')->setValue($value);
        }
    }

    /**
     * @param int $index
     * @param Row $row
     * @param Device $model
     */
    function fillRowFromModel(int $index, Row $row, $model): void
    {
        $product = $model->getProduct();

        $row->getCell('modified')->setHtml(Formatter::formatModification(
            $product->getAuctionPrice()->getModificationTime(),
            $product->getAuctionPrice()->getModificationUser()
        ));

        $row->getCell('auction_price')->setValue($product->getAuctionPrice()->getValue());
    }

    /**
     * @param int $index
     * @param Row $row
     * @param Device $prototype
     * @param Device $model
     * @param User $parentModel
     */
    function fillModelFromRow(int $index, Row $row, $prototype, $model, $parentModel): void
    {
        $value = $row->getCell('auction_price')->getValue();

        if ($value !== null) {
            $product = $model->getProduct();

            $newAuctionPrice = (int)$value ?: null;
            $oldAuctionPrice = $product->getAuctionPrice()->getValue();

            if ($newAuctionPrice !== $oldAuctionPrice) {
                $product->getAuctionPrice()->setValue($newAuctionPrice);
                $product->getAuctionPrice()->setModificationTime(new \DateTime());
                $product->getAuctionPrice()->setModificationUser($parentModel);
            }
        }
    }
}

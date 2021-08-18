<?php

namespace App\ViewModel\Device;

use App\Config;
use App\Entity\Catalog\Device;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Formatter;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\Grid;
use App\ViewModel\Grid\Row;
use App\ViewModel\Grid\Cell\Action;
use App\ViewModel\Grid\Cell\Html;
use App\ViewModel\Grid\Cell\Image;
use App\ViewModel\Grid\Cell\Link;
use App\ViewModel\Grid\Cell\Text;

class ListViewModel extends AbstractViewModel
{
    /**
     * @var Grid
     */
    private $grid;

    /**
     *
     */
    public function __construct()
    {
        $this->grid = new Grid('device-grid');

        $this->grid->setColumns([
            (new Column())->setName('Image')->setWidth(Config::IMAGE_COLUMN_WIDTH),
            (new Column())->setName('Name'),
            (new Column())->setName('Marketp. price')->setWidth(Config::PRICE_COLUMN_WIDTH),
            (new Column())->setName('Auction price')->setWidth(Config::PRICE_COLUMN_WIDTH),
            (new Column())->setName('Cost price')->setWidth(Config::PRICE_COLUMN_WIDTH),
            (new Column())->setName('Modified')->setWidth(Config::MODIFICATION_COLUMN_WIDTH),
            (new Column())->setName('Actions')->setWidth(Config::EDIT_COLUMN_WIDTH)
        ]);
    }

    /**
     * @param Device[] $devices
     * @param float[]
     */
    public function fillFromDevices(array $devices, array $deviceCosts): void
    {
        foreach ($devices as $device) {
            $row = new Row();

            $product = $device->getProduct();

            $imageCell = (new Image())
                ->setSrc($product->getImageUrl());

            $nameCell = (new Link())
                ->setText($product->getName())
                ->setHref($product->getWikiPageUrl());

            $marketplaceCell = (new Html())
                ->setHtml(Formatter::formatPrice($product->getMarketplacePrice()));

            $auctionCell = (new Html())
                ->setHtml(Formatter::formatPrice($product->getAuctionPrice()->getValue()));

            $deviceCost = null;
            if (array_key_exists($device->getId(), $deviceCosts)) {
                $deviceCost = $deviceCosts[$device->getId()];
            }

            $costCell = (new Html())
                ->setHtml(Formatter::formatPrice($deviceCost));

            $modifiedCell = (new Html())
                ->setHtml(Formatter::formatModification(
                    $product->getModificationTime(),
                    $product->getModificationUser()
                ));

            $actionsCell = (new Action())
                ->setName('Edit')
                ->setRoute('devices_edit')
                ->setParams(['id' => $device->getId()]);

            $row->setCells([
                $imageCell,
                $nameCell,
                $marketplaceCell,
                $auctionCell,
                $costCell,
                $modifiedCell,
                $actionsCell
            ]);

            $this->grid->addRow($row);
        }
    }

    /**
     * @return Grid
     */
    public function getGrid(): Grid
    {
        return $this->grid;
    }

    /**
     * @param Grid $grid
     */
    public function setGrid(Grid $grid): void
    {
        $this->grid = $grid;
    }
}

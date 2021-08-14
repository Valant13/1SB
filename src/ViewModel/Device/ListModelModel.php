<?php

namespace App\ViewModel\Device;

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

class ListModelModel extends AbstractViewModel
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
            (new Column())->setName('Image')->setWidth(15),
            (new Column())->setName('Name'),
            (new Column())->setName('Marketp.')->setWidth(15),
            (new Column())->setName('Modified')->setWidth(17),
            (new Column())->setName('Actions')->setWidth(10)
        ]);
    }

    /**
     * @param Device[] $devices
     */
    public function fillFromDevices(array $devices): void
    {
        foreach ($devices as $device) {
            $row = new Row();

            $product = $device->getProduct();

            $imageCell = (new Image())
                ->setHref($product->getImageUrl());

            $nameCell = (new Link())
                ->setText($product->getName())
                ->setHref($product->getWikiPageUrl());

            $marketplaceCell = (new Text())
                ->setText(Formatter::formatPrice($product->getMarketplacePrice()));

            $modifiedCell = (new Html())
                ->setHtml(Formatter::formatModification(
                    $product->getModificationTime(),
                    $product->getModificationUser()
                ));

            $actionsCell = (new Action())
                ->setName('Edit')
                ->setRoute('devices_edit')
                ->setParams(['id' => $device->getId()]);

            $row->setCells([$imageCell, $nameCell, $marketplaceCell, $modifiedCell, $actionsCell]);

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

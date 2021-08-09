<?php

namespace App\ViewModel\Device;

use App\Entity\Catalog\Device;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Formatter;
use App\ViewModel\Grid\Column;
use App\ViewModel\Grid\Grid;
use App\ViewModel\Grid\Row;
use App\ViewModel\Grid\Value\Action;
use App\ViewModel\Grid\Value\Html;
use App\ViewModel\Grid\Value\Image;
use App\ViewModel\Grid\Value\Link;
use App\ViewModel\Grid\Value\Text;

class ListModel extends AbstractViewModel
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
        $this->grid = new Grid();
        $this->grid->addColumn((new Column())->setName('Image')->setWidth(15));
        $this->grid->addColumn((new Column())->setName('Name'));
        $this->grid->addColumn((new Column())->setName('Marketpl')->setWidth(15));
        $this->grid->addColumn((new Column())->setName('Modified')->setWidth(15));
        $this->grid->addColumn((new Column())->setName('Actions')->setWidth(15));
    }

    /**
     * @param Device[] $devices
     */
    public function fillFromDevices(array $devices): void
    {
        foreach ($devices as $device) {
            $product = $device->getProduct();

            $row = new Row();

            $image = (new Image())
                ->setHref($product->getImageUrl());

            $name = (new Link())
                ->setText($product->getName())
                ->setHref($product->getWikiPageUrl());

            $marketplace = (new Text())
                ->setText(Formatter::formatPrice($product->getMarketplacePrice()));

            $modified = (new Html())
                ->setHtml(Formatter::formatModification(
                    $product->getModificationTime(),
                    $product->getModificationUser()
                ));

            $actions = (new Action())
                ->setName('Edit')
                ->setRoute('devices_edit')
                ->setParams(['id' => $device->getId()]);

            $row->setValues([$image, $name, $marketplace, $modified, $actions]);

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

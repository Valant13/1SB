<?php

namespace App\ViewModel\Material;

use App\Entity\Catalog\Material;
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
     * @param Material[] $materials
     */
    public function fillFromMaterials(array $materials): void
    {
        foreach ($materials as $material) {
            $product = $material->getProduct();

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
                ->setRoute('materials_edit')
                ->setParams(['id' => $material->getId()]);

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

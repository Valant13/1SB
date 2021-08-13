<?php

namespace App\ViewModel\Material;

use App\Entity\Catalog\Material;
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
        $this->grid = new Grid('material-grid');

        $this->grid->setColumns([
            (new Column())->setName('Image')->setWidth(15),
            (new Column())->setName('Name'),
            (new Column())->setName('Marketp.')->setWidth(15),
            (new Column())->setName('Modified')->setWidth(15),
            (new Column())->setName('Actions')->setWidth(15)
        ]);
    }

    /**
     * @param Material[] $materials
     */
    public function fillFromMaterials(array $materials): void
    {
        foreach ($materials as $material) {
            $row = new Row();

            $product = $material->getProduct();

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
                ->setRoute('materials_edit')
                ->setParams(['id' => $material->getId()]);

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

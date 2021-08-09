<?php

namespace App\ViewModel\Material;

use App\Entity\Catalog\Material;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Formatter;

class ListModel extends AbstractViewModel
{
    /**
     * @var ListItem[]
     */
    private $items = [];

    /**
     * @param Material[] $materials
     */
    public function fillFromMaterials(array $materials): void
    {
        foreach ($materials as $material) {
            $product = $material->getProduct();

            $item = new ListItem();

            $item->setId($material->getId());
            $item->setImageUrl($product->getImageUrl());
            $item->setWikiPageUrl($product->getWikiPageUrl());
            $item->setName($product->getName());

            $item->setMarketplacePrice(Formatter::formatPrice(
                $product->getMarketplacePrice()
            ));

            $item->setModification(Formatter::formatModification(
                $product->getModificationTime(),
                $product->getModificationUser()
            ));

            $this->items[] = $item;
        }
    }

    /**
     * @return ListItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param ListItem[] $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}

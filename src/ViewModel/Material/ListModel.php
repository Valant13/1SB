<?php

namespace App\ViewModel\Material;

use App\Entity\Catalog\Material;
use App\ViewModel\AbstractViewModel;

class ListModel extends AbstractViewModel
{
    /**
     * @var ListItem[]
     */
    private $items;

    /**
     * @param Material[] $materials
     */
    public function fillFromMaterials(array $materials): void
    {
        foreach ($materials as $material) {
            $item = new ListItem();
            $item->setId($material->getId());
            $item->setImageUrl($material->getProduct()->getImageUrl());
            $item->setName($material->getProduct()->getName());
            $item->setMarketplacePrice($material->getProduct()->getMarketplacePrice());
            // TODO: make modified string properly
            $item->setModifiedString(null);

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

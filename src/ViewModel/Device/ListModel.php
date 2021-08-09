<?php

namespace App\ViewModel\Device;

use App\Entity\Catalog\Device;
use App\ViewModel\AbstractViewModel;
use App\ViewModel\Formatter;

class ListModel extends AbstractViewModel
{
    /**
     * @var ListItem[]
     */
    private $items = [];

    /**
     * @param Device[] $devices
     */
    public function fillFromDevices(array $devices): void
    {
        foreach ($devices as $device) {
            $product = $device->getProduct();

            $item = new ListItem();

            $item->setId($device->getId());
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

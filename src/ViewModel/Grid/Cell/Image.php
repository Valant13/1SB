<?php

namespace App\ViewModel\Grid\Cell;

use App\ViewModel\Grid\AbstractCell;
use App\ViewModel\Grid\CellInterface;

class Image extends AbstractCell implements CellInterface
{
    /**
     * @var string|null
     */
    private $src;

    /**
     * @var string|null
     */
    private $alt;

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'image';
    }

    /**
     * @return string|null
     */
    public function getSrc(): ?string
    {
        return $this->src;
    }

    /**
     * @param string|null $src
     * @return Image
     */
    public function setSrc(?string $src): Image
    {
        $this->src = $src;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlt(): ?string
    {
        return $this->alt;
    }

    /**
     * @param string|null $alt
     * @return Image
     */
    public function setAlt(?string $alt): Image
    {
        $this->alt = $alt;
        return $this;
    }
}

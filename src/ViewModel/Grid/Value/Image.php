<?php

namespace App\ViewModel\Grid\Value;

use App\ViewModel\Grid\ValueInterface;

class Image implements ValueInterface
{
    /**
     * @var string|null
     */
    private $href;

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
    public function getHref(): ?string
    {
        return $this->href;
    }

    /**
     * @param string|null $href
     * @return Image
     */
    public function setHref(?string $href): Image
    {
        $this->href = $href;
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

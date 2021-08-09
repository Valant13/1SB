<?php

namespace App\ViewModel\Grid\Value;

use App\ViewModel\Grid\ValueInterface;

class Link implements ValueInterface
{
    /**
     * @var string|null
     */
    private $href;

    /**
     * @var string|null
     */
    private $text;

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'link';
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
     * @return Link
     */
    public function setHref(?string $href): Link
    {
        $this->href = $href;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return Link
     */
    public function setText(?string $text): Link
    {
        $this->text = $text;
        return $this;
    }
}

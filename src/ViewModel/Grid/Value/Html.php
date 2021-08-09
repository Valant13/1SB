<?php

namespace App\ViewModel\Grid\Value;

use App\ViewModel\Grid\ValueInterface;

class Html implements ValueInterface
{
    /**
     * @var string|null
     */
    private $html;

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'html';
    }

    /**
     * @return string|null
     */
    public function getHtml(): ?string
    {
        return $this->html;
    }

    /**
     * @param string|null $html
     * @return Html
     */
    public function setHtml(?string $html): Html
    {
        $this->html = $html;
        return $this;
    }
}

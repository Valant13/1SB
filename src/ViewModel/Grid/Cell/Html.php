<?php

namespace App\ViewModel\Grid\Cell;

use App\ViewModel\Grid\AbstractCell;
use App\ViewModel\Grid\CellInterface;

class Html extends AbstractCell implements CellInterface
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

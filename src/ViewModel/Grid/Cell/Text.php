<?php

namespace App\ViewModel\Grid\Cell;

use App\ViewModel\Grid\CellInterface;

class Text implements CellInterface
{
    /**
     * @var string|null
     */
    private $text;

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'text';
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
     * @return Text
     */
    public function setText(?string $text): Text
    {
        $this->text = $text;
        return $this;
    }
}

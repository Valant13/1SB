<?php

namespace App\ViewModel\Grid\Value;

use App\ViewModel\Grid\ValueInterface;

class Text implements ValueInterface
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

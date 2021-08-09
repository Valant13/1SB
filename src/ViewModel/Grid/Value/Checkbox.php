<?php

namespace App\ViewModel\Grid\Value;

use App\ViewModel\Grid\ValueInterface;

class Checkbox implements ValueInterface
{
    /**
     * @var bool
     */
    private $isChecked = false;

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'checkbox';
    }

    /**
     * @return bool
     */
    public function isChecked(): bool
    {
        return $this->isChecked;
    }

    /**
     * @param bool $isChecked
     * @return Checkbox
     */
    public function setIsChecked(bool $isChecked): Checkbox
    {
        $this->isChecked = $isChecked;
        return $this;
    }
}

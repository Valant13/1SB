<?php

namespace App\ViewModel\Grid\Cell;

use App\ViewModel\Grid\CellInterface;

class Checkbox implements CellInterface
{
    /**
     * @var string|null
     */
    private $name;

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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Checkbox
     */
    public function setName(?string $name): Checkbox
    {
        $this->name = $name;
        return $this;
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

<?php

namespace App\ViewModel\Grid;

class Column
{
    const CONTROL_TYPE_NONE = 'none';
    const CONTROL_TYPE_CLEAR = 'clear';
    const CONTROL_TYPE_SELECT_UNSELECT = 'select_unselect';

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var int|null
     */
    private $width;

    /**
     * @var string
     */
    private $controlType = self::CONTROL_TYPE_NONE;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Column
     */
    public function setName(?string $name): Column
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     * @return Column
     */
    public function setWidth(?int $width): Column
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return string
     */
    public function getControlType(): string
    {
        return $this->controlType;
    }

    /**
     * @param string $controlType
     * @return Column
     */
    public function setControlType(string $controlType): Column
    {
        $allowedTypes = [
            self::CONTROL_TYPE_NONE,
            self::CONTROL_TYPE_CLEAR,
            self::CONTROL_TYPE_SELECT_UNSELECT
        ];

        if (!in_array($controlType, $allowedTypes)) {
            throw new \InvalidArgumentException();
        }

        $this->controlType = $controlType;
        return $this;
    }
}

<?php

namespace App\ViewModel\Grid;

class Row
{
    /**
     * @var string|null
     */
    private $style;

    /**
     * @var CellInterface[]
     */
    private $cells = [];

    /**
     * @return string|null
     */
    public function getStyle(): ?string
    {
        return $this->style;
    }

    /**
     * @param string|null $style
     */
    public function setStyle(?string $style): void
    {
        $this->style = $style;
    }

    /**
     * @return CellInterface[]
     */
    public function getCells(): array
    {
        return $this->cells;
    }

    /**
     * @param CellInterface[] $cells
     */
    public function setCells(array $cells): void
    {
        $this->cells = $cells;
    }

    /**
     * @param string $key
     * @return CellInterface
     */
    public function getCell(string $key): CellInterface
    {
        return $this->cells[$key];
    }

    /**
     * @param string $key
     * @param CellInterface $cell
     */
    public function setCell(string $key, CellInterface $cell): void
    {
        $this->cells[$key] = $cell;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasCell(string $key): bool
    {
        return array_key_exists($key, $this->cells);
    }
}

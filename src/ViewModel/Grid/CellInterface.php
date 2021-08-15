<?php

namespace App\ViewModel\Grid;

interface CellInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return int
     */
    public function getColspan(): int;

    /**
     * @param int $colspan
     * @return CellInterface
     */
    public function setColspan(int $colspan): CellInterface;

    /**
     * @return int
     */
    public function getRowspan(): int;

    /**
     * @param int $rowspan
     * @return CellInterface
     */
    public function setRowspan(int $rowspan): CellInterface;
}

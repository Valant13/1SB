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
     * @return int
     */
    public function getRowspan(): int;
}

<?php

namespace App\ViewModel\Grid;

interface CellInterface
{
    /**
     * @return string
     */
    public function getType(): string;
}

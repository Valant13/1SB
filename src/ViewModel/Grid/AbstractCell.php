<?php

namespace App\ViewModel\Grid;

abstract class AbstractCell implements CellInterface
{
    /**
     * @var int
     */
    private $colspan = 1;

    /**
     * @var int
     */
    private $rowspan = 1;

    /**
     * @return int
     */
    public function getColspan(): int
    {
        return $this->colspan;
    }

    /**
     * @param int $colspan
     */
    public function setColspan(int $colspan): void
    {
        $this->colspan = $colspan;
    }

    /**
     * @return int
     */
    public function getRowspan(): int
    {
        return $this->rowspan;
    }

    /**
     * @param int $rowspan
     */
    public function setRowspan(int $rowspan): void
    {
        $this->rowspan = $rowspan;
    }
}
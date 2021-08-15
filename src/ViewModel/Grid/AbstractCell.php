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
     * @return CellInterface
     */
    public function setColspan(int $colspan): CellInterface
    {
        $this->colspan = $colspan;
        return $this;
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
     * @return CellInterface
     */
    public function setRowspan(int $rowspan): CellInterface
    {
        $this->rowspan = $rowspan;
        return $this;
    }
}
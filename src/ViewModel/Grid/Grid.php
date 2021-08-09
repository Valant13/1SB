<?php

namespace App\ViewModel\Grid;

class Grid
{
    /**
     * @var string|null
     */
    private $idForJs;

    /**
     * @var Column[]
     */
    private $columns = [];

    /**
     * @var Row[];
     */
    private $rows = [];

    /**
     * @return string|null
     */
    public function getIdForJs(): ?string
    {
        return $this->idForJs;
    }

    /**
     * @param string|null $idForJs
     * @return Grid
     */
    public function setIdForJs(?string $idForJs): Grid
    {
        $this->idForJs = $idForJs;
        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Column[] $columns
     * @return Grid
     */
    public function setColumns(array $columns): Grid
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param Column $column
     * @return Grid
     */
    public function addColumn(Column $column): Grid
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @return Row[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param Row[] $rows
     * @return Grid
     */
    public function setRows(array $rows): Grid
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * @param int $index
     * @return Row
     */
    public function getRow(int $index): Row
    {
        return $this->rows[$index];
    }

    /**
     * @param int $index
     * @param Row $row
     * @return Grid
     */
    public function setRow(int $index, Row $row): Grid
    {
        $this->rows[$index] = $row;
        return $this;
    }

    /**
     * @param Row $row
     * @return Grid
     */
    public function addRow(Row $row): Grid
    {
        $this->rows[] = $row;
        return $this;
    }
}

<?php

namespace App\ViewModel\Grid;

class Grid
{
    /**
     * @var string|null
     */
    private $name;

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
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Grid
     */
    public function setName(?string $name): Grid
    {
        $this->name = $name;
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
     * @param Row $row
     * @return Grid
     */
    public function addRow(Row $row): Grid
    {
        $this->rows[] = $row;
        return $this;
    }
}

<?php

namespace App\ViewModel\Grid;

use Symfony\Component\HttpFoundation\Request;

class Grid
{
    /**
     * @var string|null
     */
    private $idForJs;

    /**
     * @var GridBindingInterface
     */
    private $binding;

    /**
     * @var Column[]
     */
    private $columns = [];

    /**
     * @var Row[];
     */
    private $rows = [];

    /**
     * @param string $idForJs
     * @param GridBindingInterface $binding
     * @param array $prototypes
     */
    public function __construct(string $idForJs, GridBindingInterface $binding, $prototypes)
    {
        $this->idForJs = $idForJs;
        $this->binding = $binding;

        foreach ($prototypes as $prototype) {
            $index = $this->binding->getPrototypeIndex($prototype);

            $this->rows[$index] = $this->binding->buildRow($index, $prototype);
        }
    }

    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void
    {
        foreach ($this->rows as $index => $row) {
            $requestValues = [];

            foreach ($this->binding->getRequestValueMapping() as $columnKey => $requestKey) {
                $requestValues[$columnKey] = $this->getRequestValue($index, $requestKey, $request);
            }

            $this->binding->fillRowFromRequest($index, $row, $requestValues);
        }
    }

    /**
     * @param array $models
     */
    public function fillFromModels(array $models): void
    {
        $indexedModels = $this->getIndexedModels($models);

        foreach ($this->rows as $index => $row) {
            $this->binding->fillRowFromModel($index, $row, $indexedModels[$index]);
        }
    }

    /**
     * @param array $models
     * @param $parentModel
     */
    public function fillModels(array $models, $parentModel): void
    {
        $indexedModels = $this->getIndexedModels($models);

        foreach ($this->rows as $index => $row) {
            $this->binding->fillModelFromRow($index, $row, $indexedModels[$index], $parentModel);
        }
    }

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

    /**
     * @param int $index
     * @return bool
     */
    public function hasRow(int $index): bool
    {
        return array_key_exists($index, $this->rows);
    }

    /**
     * @param int $rowIndex
     * @param string $requestKey
     * @param Request $request
     * @return string|null
     */
    private function getRequestValue(int $rowIndex, string $requestKey, Request $request): ?string
    {
        $param = $request->request->get($requestKey);

        if (is_array($param)) {
            if (array_key_exists($rowIndex, $param)) {
                return $param[$rowIndex];
            }
        }

        return null;
    }

    /**
     * @param array $models
     * @return array
     */
    private function getIndexedModels(array $models): array
    {
        $indexedModels = [];

        foreach ($models as $model) {
            $index = $this->binding->getModelIndex($model);
            $indexedModels[$index] = $model;
        }

        return $indexedModels;
    }
}

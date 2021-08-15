<?php

namespace App\ViewModel\Grid;

use Symfony\Component\HttpFoundation\Request;

class Grid
{
    /**
     * @var string
     */
    private $idForJs;

    /**
     * @var bool
     */
    private $isStriped = true;

    /**
     * @var GridBindingInterface
     */
    private $binding;

    /**
     * @var array
     */
    private $prototypes = [];

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
     * @param GridBindingInterface|null $binding
     * @param array $prototypes
     */
    public function __construct(string $idForJs, GridBindingInterface $binding = null, $prototypes = [])
    {
        $this->idForJs = $idForJs;
        $this->binding = $binding;

        if ($this->binding !== null) {
            $this->columns = $this->binding->buildColumns();

            foreach ($prototypes as $prototype) {
                $index = $this->binding->getPrototypeIndex($prototype);

                $this->prototypes[$index] = $prototype;
                $this->rows[$index] = $this->binding->buildRow($index, $prototype);
            }
        }
    }

    /**
     * @param Request $request
     * @throws \BadMethodCallException
     */
    public function fillFromRequest(Request $request): void
    {
        if ($this->binding === null) {
            throw new \BadMethodCallException('Binding is not set');
        }

        foreach ($this->rows as $index => $row) {
            $requestValues = [];

            foreach ($this->binding->getRequestValueKeys() as $requestKey) {
                $requestValues[$requestKey] = $this->getRowRequestValue($index, $requestKey, $request);
            }

            $this->binding->fillRowFromRequest($index, $row, $requestValues);
        }
    }

    /**
     * @param array $models
     * @throws \BadMethodCallException
     */
    public function fillFromModels(array $models): void
    {
        if ($this->binding === null) {
            throw new \BadMethodCallException('Binding is not set');
        }

        $indexedModels = $this->getIndexedModels($models);

        foreach ($this->rows as $index => $row) {
            $model = null;
            if (array_key_exists($index, $indexedModels)) {
                $model = $indexedModels[$index];
            }

            $this->binding->fillRowFromModel($index, $row, $model);
        }
    }

    /**
     * @param array $models
     * @param $parentModel
     * @throws \Exception
     */
    public function fillModels(array $models, $parentModel): void
    {
        if ($this->binding === null) {
            throw new \Exception('Binding is not set');
        }

        $indexedModels = $this->getIndexedModels($models);

        foreach ($this->rows as $index => $row) {
            $model = null;
            if (array_key_exists($index, $indexedModels)) {
                $model = $indexedModels[$index];
            }

            $prototype = $this->prototypes[$index];

            $this->binding->fillModelFromRow($index, $row, $prototype, $model, $parentModel);
        }
    }

    /**
     * @return string
     */
    public function getIdForJs(): string
    {
        return $this->idForJs;
    }

    /**
     * @return bool
     */
    public function isStriped(): bool
    {
        return $this->isStriped;
    }

    /**
     * @param bool $isStriped
     */
    public function setIsStriped(bool $isStriped): void
    {
        $this->isStriped = $isStriped;
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
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
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
     */
    public function setRows(array $rows): void
    {
        $this->rows = $rows;
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
     */
    public function setRow(int $index, Row $row): void
    {
        $this->rows[$index] = $row;
    }

    /**
     * @param Row $row
     */
    public function addRow(Row $row): void
    {
        $this->rows[] = $row;
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
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->rows) === 0;
    }

    /**
     * @param int $rowIndex
     * @param string $requestKey
     * @param Request $request
     * @return string|null
     */
    private function getRowRequestValue(int $rowIndex, string $requestKey, Request $request): ?string
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

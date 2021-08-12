<?php

namespace App\ViewModel\Grid;

interface GridBindingInterface
{
    /**
     * @param $prototype
     * @return int
     */
    function getPrototypeIndex($prototype): int;

    /**
     * @param $model
     * @return int
     */
    function getModelIndex($model): int;

    /**
     * @return string[]
     */
    function getRequestValueKeys(): array;

    /**
     * @return Column[]
     */
    function buildColumns(): array;

    /**
     * @param int $index
     * @param $prototype
     * @return Row
     */
    function buildRow(int $index, $prototype): Row;

    /**
     * @param int $index
     * @param Row $row
     * @param string[] $requestValues
     */
    function fillRowFromRequest(int $index, Row $row, array $requestValues): void;

    /**
     * @param int $index
     * @param Row $row
     * @param $model
     */
    function fillRowFromModel(int $index, Row $row, $model): void;

    /**
     * @param int $index
     * @param Row $row
     * @param $prototype
     * @param $model
     * @param $parentModel
     */
    function fillModelFromRow(int $index, Row $row, $prototype, $model, $parentModel): void;
}

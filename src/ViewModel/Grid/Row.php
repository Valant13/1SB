<?php

namespace App\ViewModel\Grid;

class Row
{
    /**
     * @var ValueInterface[]
     */
    private $values = [];

    /**
     * @return ValueInterface[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param ValueInterface[] $values
     * @return Row
     */
    public function setValues(array $values): Row
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param ValueInterface $value
     * @return Row
     */
    public function addValue(ValueInterface $value): Row
    {
        $this->values[] = $value;
        return $this;
    }
}

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
     * @param string $key
     * @return ValueInterface
     */
    public function getValue(string $key): ValueInterface
    {
        return $this->values[$key];
    }

    /**
     * @param string $key
     * @param ValueInterface $value
     * @return Row
     */
    public function setValue(string $key, ValueInterface $value): Row
    {
        $this->values[$key] = $value;
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

    /**
     * @param string $key
     * @return bool
     */
    public function hasValue(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }
}

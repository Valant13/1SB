<?php

namespace App\ViewModel\Grid\Value;

use App\ViewModel\Grid\ValueInterface;

class Field implements ValueInterface
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $value;

    /**
     * @var string|null
     */
    private $valueType;

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'field';
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Field
     */
    public function setName(?string $name): Field
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param string|null $value
     * @return Field
     */
    public function setValue(?string $value): Field
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getValueType(): ?string
    {
        return $this->valueType;
    }

    /**
     * @param string|null $valueType
     * @return Field
     */
    public function setValueType(?string $valueType): Field
    {
        $this->valueType = $valueType;
        return $this;
    }
}

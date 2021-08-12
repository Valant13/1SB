<?php

namespace App\ViewModel\Grid\Cell;

use App\ViewModel\Grid\CellInterface;

class Action implements CellInterface
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $route;

    /**
     * @var string[]|null
     */
    private $params;

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return 'action';
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
     * @return Action
     */
    public function setName(?string $name): Action
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @param string|null $route
     * @return Action
     */
    public function setRoute(?string $route): Action
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getParams(): ?array
    {
        return $this->params;
    }

    /**
     * @param string[]|null $params
     * @return Action
     */
    public function setParams(?array $params): Action
    {
        $this->params = $params;
        return $this;
    }
}

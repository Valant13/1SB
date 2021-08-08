<?php

namespace App\ViewModel;

use Symfony\Component\HttpFoundation\Request;

class AbstractViewModel implements ViewModelInterface
{
    /**
     * @var string[]
     */
    protected $errors = [];

    /**
     * @inheritDoc
     */
    public function fillByRequest(Request $request): void
    {

    }

    /**
     * @inheritDoc
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @inheritDoc
     */
    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    /**
     * @inheritDoc
     */
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
}
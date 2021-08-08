<?php

namespace App\ViewModel;

use Symfony\Component\HttpFoundation\Request;

interface ViewModelInterface
{
    /**
     * @param Request $request
     */
    public function fillByRequest(Request $request): void;

    /**
     * @param string[]
     */
    public function getErrors(): array;

    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void;

    /**
     * @return bool
     */
    public function hasErrors(): bool;

    /**
     * @param string $error
     */
    public function addError(string $error): void;
}

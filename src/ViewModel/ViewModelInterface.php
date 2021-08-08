<?php

namespace App\ViewModel;

use Symfony\Component\HttpFoundation\Request;

interface ViewModelInterface
{
    /**
     * @param Request $request
     */
    public function fillFromRequest(Request $request): void;

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

    /**
     * @param string[]
     */
    public function getNotices(): array;

    /**
     * @param array $notices
     */
    public function setNotices(array $notices): void;

    /**
     * @return bool
     */
    public function hasNotices(): bool;

    /**
     * @param string $notice
     */
    public function addNotice(string $notice): void;
}

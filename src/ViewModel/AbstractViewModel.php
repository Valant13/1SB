<?php

namespace App\ViewModel;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;

abstract class AbstractViewModel implements ViewModelInterface
{
    /**
     * @var string[]
     */
    protected $errors = [];

    /**
     * @var string[]
     */
    protected $notices = [];

    /**
     * @inheritDoc
     */
    public function fillFromRequest(Request $request): void
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

    /**
     * @inheritDoc
     */
    public function addErrors(array $errors): void
    {
        $this->errors = array_merge($this->errors, $errors);
    }

    /**
     * @inheritDoc
     */
    public function addErrorsFromViolations(ConstraintViolationListInterface $violationList): void
    {
        foreach ($violationList as $violation) {
            $this->errors[] = $violation->getMessage();
        }
    }

    /**
     * @inheritDoc
     */
    public function getNotices(): array
    {
        return $this->notices;
    }

    /**
     * @inheritDoc
     */
    public function setNotices(array $notices): void
    {
        $this->notices = $notices;
    }

    /**
     * @inheritDoc
     */
    public function hasNotices(): bool
    {
        return count($this->notices) > 0;
    }

    /**
     * @inheritDoc
     */
    public function addNotice(string $notice): void
    {
        $this->notices[] = $notice;
    }
}

<?php

namespace Biig\Melodiia\Response\Model;

class UserDataError
{
    /**
     * @var string
     */
    private $propertyPath;

    /**
     * @var string[]
     */
    private $errors;

    /**
     * ApiError constructor.
     *
     * @param string   $propertyPath
     * @param string[] $errors
     */
    public function __construct(string $propertyPath, array $errors)
    {
        $this->propertyPath = $propertyPath;
        $this->errors = $errors;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        return $this->propertyPath;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $error
     */
    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }
}

<?php

namespace Flamix\Marketing\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function __construct(array $errors)
    {
        parent::__construct('Validation errors: ' . print_r($errors, true), 422);
    }
}

<?php

namespace Flamix\Marketing\Exceptions;

use Exception;

/** Thrown on HTTP 422; the message holds the validation errors. */
class ValidationException extends Exception
{
    public function __construct(array $errors)
    {
        parent::__construct('Validation errors: ' . print_r($errors, true), 422);
    }
}

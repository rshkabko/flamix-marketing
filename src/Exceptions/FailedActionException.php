<?php

namespace Flamix\Marketing\Exceptions;

use Exception;

class FailedActionException extends Exception
{
    public function __construct(array $message)
    {
        parent::__construct('Errors: ' . print_r($message, true), 400);
    }
}

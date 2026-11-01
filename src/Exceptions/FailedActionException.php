<?php

namespace Flamix\Marketing\Exceptions;

use Exception;

/** Thrown on HTTP 400; the message holds the API response. */
class FailedActionException extends Exception
{
    public function __construct(array $message)
    {
        parent::__construct('Errors: ' . print_r($message, true), 400);
    }
}

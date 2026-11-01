<?php

namespace Flamix\Marketing\Exceptions;

use Exception;

/** Thrown on HTTP 401: the token is wrong or missing. */
class UnauthorizedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Please, provide valide auth code!', 401);
    }
}

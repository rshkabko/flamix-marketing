<?php

namespace Flamix\Marketing\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    public function __construct()
    {
        parent::__construct('Please, provide valide auth code!', 401);
    }
}

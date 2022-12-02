<?php

namespace Flamix\Marketing\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Not found!', 404);
    }
}

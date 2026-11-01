<?php

namespace Flamix\Marketing\Exceptions;

use Exception;

/** Thrown on HTTP 404. */
class NotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Not found!', 404);
    }
}

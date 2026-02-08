<?php

namespace App\Models;

use Exception;

class MinDateUndercutException extends Exception
{
    function __construct($message = '', $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
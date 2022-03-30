<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ActionNotAllowedException extends Exception
{
    public function __construct(string $message = "Action Not Allowed", Throwable $previous = null)
    {
        $this->message = $message;
        parent::__construct($this->message, ExceptionCodes::ACTION_NOT_ALLOWED, $previous);
    }
}

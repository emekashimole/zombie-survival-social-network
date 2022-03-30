<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ResourceNotFoundException extends Exception
{
    public function __construct($message = "Resource Not Found", Throwable $previous = null)
    {
        $this->message = $message;
        parent::__construct($this->message, ExceptionCodes::RESOURCE_NOT_FOUND, $previous);
    }
}

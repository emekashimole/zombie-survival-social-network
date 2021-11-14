<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class InfectedSurvivorException extends Exception
{
    public function __construct($message = "Survivor has been infected", Throwable $previous = null)
    {
        $this->message = $message;
        parent::__construct($this->message, $code = ExceptionCodes::INFECTED_SURVIVOR, $previous);
    }
}

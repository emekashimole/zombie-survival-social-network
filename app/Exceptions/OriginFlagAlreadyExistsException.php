<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class OriginFlagAlreadyExistsException extends Exception
{
    protected $message = "You have already flagged this survivor as infected";

    public function __construct(Throwable $previous = null)
    {
        parent::__construct($this->message, ExceptionCodes::ORIGIN_FLAG_ALREADY_EXISTS, $previous);
    }
}

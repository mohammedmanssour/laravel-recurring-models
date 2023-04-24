<?php

namespace MohammedManssour\LaravelRecurringModels\Exceptions;

use Throwable;

class DriverNotSupportedException extends \Exception
{
    public function __construct(string $driver, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct("Database driver \"{$driver}\" is not supported.", $code, $previous);
    }
}

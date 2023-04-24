<?php

namespace MohammedManssour\LaravelRecurringModels\Exceptions;

use Throwable;

class ComplexRepetionEndsAfterNotAvailableException extends \Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct('endsAfter method is not available for complex repetitions. Please use endsAt method instead to explicitly set end date.', $code, $previous);
    }
}

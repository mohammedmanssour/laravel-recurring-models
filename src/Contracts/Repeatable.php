<?php

namespace MohammedManssour\LaravelRecurringModels\Contracts;

use Carbon\CarbonInterface as Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use MohammedManssour\LaravelRecurringModels\Support\Repeat;

interface Repeatable
{
    public function repetitions(): MorphMany;

    public function repetitionBaseDate(): Carbon;

    public function repeat(): Repeat;
}

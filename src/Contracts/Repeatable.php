<?php

namespace MohammedManssour\LaravelRecurringModels\Contracts;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Repeatable
{
    public function repetitions(): MorphMany;

    public function startsAt(): Carbon;
}

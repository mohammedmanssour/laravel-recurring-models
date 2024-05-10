<?php

namespace MohammedManssour\LaravelRecurringModels\Contracts;

use Carbon\CarbonInterface as Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Models\Repetition;
use MohammedManssour\LaravelRecurringModels\Support\Repeat;
use MohammedManssour\LaravelRecurringModels\Support\RepeatCollection;

/**
 * @property-read RepeatCollection $repetitions
 *
 * @method MorphMany<int, RepeatCollection> repetitions()
 */
interface Repeatable
{
    /** @return MorphMany<Repetition> */
    public function repetitions(): MorphMany;

    public function repetitionBaseDate(?RepetitionType $type = null): Carbon;

    public function repeat(): Repeat;
}

<?php

namespace MohammedManssour\LaravelRecurringModels\Support;

use Carbon\CarbonInterface as Carbon;
use Illuminate\Database\Eloquent\Collection;
use MohammedManssour\LaravelRecurringModels\Models\Repetition;

/**
 * @extends Collection<int, Repetition>
 */
class RepeatCollection extends Collection
{
    public function save(): void
    {
        Repetition::insert($this->map(function (Repetition $item) {
            $item->updateTimestamps();

            return $item->getAttributes();
        })->toArray());
    }

    public function nextOccurrence(Carbon $after): ?Carbon
    {
        $occurrences = $this->map(fn (Repetition $item) => $item->nextOccurrence($after))->filter()->toArray();

        if (empty($occurrences)) {
            return null;
        }

        return min($occurrences);
    }
}

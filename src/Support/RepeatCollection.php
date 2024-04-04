<?php

namespace MohammedManssour\LaravelRecurringModels\Support;

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
}

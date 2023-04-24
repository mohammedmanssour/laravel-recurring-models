<?php

namespace MohammedManssour\LaravelRecurringModels\Support;

use Illuminate\Database\Eloquent\Collection;
use MohammedManssour\LaravelRecurringModels\Models\Repetition;

class RepeatCollection extends Collection
{
    public function save()
    {
        $this->transform(fn ($item) => $item->getAttributes());
        Repetition::insert($this->items);
    }
}

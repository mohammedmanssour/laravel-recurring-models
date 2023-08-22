<?php

namespace MohammedManssour\LaravelRecurringModels\Support;

use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable;
use MohammedManssour\LaravelRecurringModels\Support\PendingRepeats\PendingComplexRepeat;
use MohammedManssour\LaravelRecurringModels\Support\PendingRepeats\PendingEveryNDaysRepeat;
use MohammedManssour\LaravelRecurringModels\Support\PendingRepeats\PendingEveryWeekRepeat;

class Repeat
{
    protected Repeatable $model;

    public function __construct(Repeatable $model)
    {
        $this->model = $model;
    }

    public function everyNDays(int $days): PendingEveryNDaysRepeat
    {
        return new PendingEveryNDaysRepeat($this->model, $days);
    }

    public function daily(): PendingEveryNDaysRepeat
    {
        return $this->everyNDays(1);
    }

    public function weekly(): PendingEveryWeekRepeat
    {
        return new PendingEveryWeekRepeat($this->model);
    }

    public function complex(string $year = '*', string $month = '*', string $day = '*', string $week = '*', string $weekOfMonth = '*', string $weekday = '*'): PendingComplexRepeat
    {
        return (new PendingComplexRepeat($this->model))->rule($year, $month, $day, $week, $weekOfMonth, $weekday);
    }
}

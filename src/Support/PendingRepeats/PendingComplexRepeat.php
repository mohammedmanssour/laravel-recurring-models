<?php

namespace MohammedManssour\LaravelRecurringModels\Support\PendingRepeats;

use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Exceptions\RepetitionEndsAfterNotAvailableException;

class PendingComplexRepeat extends PendingRepeat
{
    private array $rule;

    public function __construct(
        Repeatable $model
    ) {
        parent::__construct($model);
        $this->start_at = $this->model->repetitionBaseDate(RepetitionType::Complex)->toImmutable()->addDay();
    }

    public function rule(string $year = '*', string $month = '*', string $day = '*', string $week = '*', string $weekOfMonth = '*', string $weekday = '*'): static
    {
        $this->rule = [
            'type' => RepetitionType::Complex,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'week' => $week,
            'week_of_month' => $weekOfMonth,
            'weekday' => $weekday,
        ];

        return $this;
    }

    /**
     * calculate the end_at based on # of times wanted
     */
    public function endsAfter(int $times): static
    {
        throw new RepetitionEndsAfterNotAvailableException();
    }

    /**
     * translates repeat requirements to repeat settings array
     */
    public function rules(): array
    {
        return [[
            ...$this->rule,
            'start_at' => $this->start_at->utc(),
            'tz_offset' => $this->start_at->offset,
            'end_at' => $this->end_at,
        ]];
    }
}

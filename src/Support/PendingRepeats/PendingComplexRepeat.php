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
        $this->start_at = $this->model->repetitionBaseDate(RepetitionType::Complex)->clone()->addDay();
    }

    public function rule(string $year = '*', string $month = '*', string $day = '*', string $week = '*', string $weekday = '*'): static
    {
        $this->rule = [
            'type' => RepetitionType::Complex,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'week' => $week,
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
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
        ]];
    }
}

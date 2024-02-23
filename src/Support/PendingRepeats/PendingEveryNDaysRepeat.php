<?php

namespace MohammedManssour\LaravelRecurringModels\Support\PendingRepeats;

use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;

class PendingEveryNDaysRepeat extends PendingRepeat
{
    public function __construct(Repeatable $model, int $days)
    {
        parent::__construct($model);
        $this->interval = $days * 86400;
        $this->start_at = $this->model->repetitionBaseDate(RepetitionType::Simple)->toImmutable()->addSeconds($this->interval);
    }

    public function endsAfter(int $times): static
    {
        $this->end_at = clone $this->start_at;
        $this->end_at->addSeconds($times * $this->interval);

        return $this;
    }

    public function rules(): array
    {
        return [
            [
                'start_at' => $this->start_at->utc(),
                'tz_offset' => $this->start_at->offset,
                'interval' => $this->interval,
                'end_at' => $this->end_at,
            ],
        ];
    }
}

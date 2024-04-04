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
    }

    public function endsAfter(int $times): static
    {
        return $this->endsAt($this->start_at->addSeconds($times * $this->interval));
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

    public function type(): RepetitionType
    {
        return RepetitionType::Simple;
    }
}

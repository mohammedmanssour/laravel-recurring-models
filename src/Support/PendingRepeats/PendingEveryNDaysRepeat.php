<?php

namespace MohammedManssour\LaravelRecurringModels\Support\PendingRepeats;

use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable;

class PendingEveryNDaysRepeat extends PendingRepeat
{
    public function __construct(Repeatable $model, int $days)
    {
        parent::__construct($model);
        $this->interval = $days * 86400;
        $this->start_at = $this->model->startsAt()->clone()->addSeconds($this->interval);
    }

    public function endsAfter(int $times): static
    {
        $this->end_at = $this->start_at->clone()->addSeconds($times * $this->interval);

        return $this;
    }

    public function rules(): array
    {
        return [
            [
                'start_at' => $this->start_at,
                'interval' => $this->interval,
                'end_at' => $this->end_at,
            ],
        ];
    }
}

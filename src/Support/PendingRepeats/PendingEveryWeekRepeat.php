<?php

namespace MohammedManssour\LaravelRecurringModels\Support\PendingRepeats;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable;

class PendingEveryWeekRepeat extends PendingRepeat
{
    /**
     * days
     *
     * @var Collection<integer, object>
     */
    private Collection $days;

    private int $times = 0;

    private Collection $rules;

    public function __construct(Repeatable $model)
    {
        parent::__construct($model);
        $this->interval = 7 * 86400;
        $this->days = collect([]);
        $this->rules = collect([]);
    }

    /**
     * repeat every week on specific days
     *
     * $days acceptable = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']
     */
    public function on(array $days): static
    {
        $this->days = collect(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'])
            ->intersect($days)
            ->map(fn ($day) => (object) [
                'day' => $day,
                'date' => $this->model->startsAt()->clone()->next($day),
            ])
            ->sort(fn ($dayA, $dayB) => $dayA->date->gte($dayB->date))
            ->values();

        return $this;
    }

    public function endsAfter(int $times): static
    {
        $this->times = $times;

        return $this;
    }

    public function rules(): array
    {
        if ($this->rules->isEmpty()) {
            $this->makeRules();
        }

        return $this->rules->toArray();
    }

    private function makeRules(): void
    {
        if ($this->days->isEmpty()) {
            $this->makeGenericWeeklyRule();

            return;
        }

        $this->rules = $this->days->map(fn ($day) => [
            'start_at' => $day->date,
            'interval' => $this->interval,
        ]);

        $endAt = $this->findEndAt($this->rules->first()['start_at']);

        $this->rules->transform(fn ($rule) => [
            ...$rule,
            'end_at' => $endAt,
        ]);
    }

    private function makeGenericWeeklyRule()
    {
        $start_at = $this->model->startsAt()->clone()->addSeconds($this->interval);
        $this->rules->push([
            'start_at' => $start_at,
            'interval' => $this->interval,
            'end_at' => $this->findEndAt($start_at),
        ]);
    }

    private function findEndAt(Carbon $startAt): ?Carbon
    {
        if ($this->end_at) {
            return $this->end_at;
        }

        if (! $this->times) {
            return null;
        }

        if ($this->days->isEmpty()) {
            return $startAt->clone()->addSeconds($this->times * $this->interval);
        }

        $endAt = $startAt->clone()->addSeconds(floor($this->times / $this->days->count()) * $this->interval);
        $index = $this->times % $this->days->count() - 1;

        if ($index < 0) {
            return $endAt;
        }
        $endAt->next($this->days->get($index)->day);

        return $endAt;
    }
}

<?php

namespace MohammedManssour\LaravelRecurringModels\Support\PendingRepeats;

use Illuminate\Support\Collection;
use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable;
use MohammedManssour\LaravelRecurringModels\Exceptions\RepetitionEndsAfterNotAvailableException;

class PendingEveryWeekRepeat extends PendingRepeat
{
    /**
     * days
     *
     * @var Collection<integer, object>
     */
    private Collection $days;

    private Collection $rules;

    public function __construct(Repeatable $model)
    {
        parent::__construct($model);
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
        $this->days = collect($this->weekdays())
            ->intersect($days)
            ->values();

        return $this;
    }

    public function endsAfter(int $times): static
    {
        throw new RepetitionEndsAfterNotAvailableException();
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
            $this->rules->push(
                $this->getRule(
                    strtolower($this->model->repetitionBaseDate()->format('l'))
                )
            );

            return;
        }

        $this->rules = $this->days->map(fn ($day) => $this->getRule($day));
    }

    private function getRule(string $day): array
    {
        $complexPattern = (new PendingComplexRepeat($this->model))
            ->rule(weekday: array_search($day, $this->weekdays()));

        if ($this->end_at) {
            $complexPattern->endsAt($this->end_at);
        }

        $rules = $complexPattern->rules();

        return $rules[0];
    }

    private function weekdays()
    {
        return ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    }
}

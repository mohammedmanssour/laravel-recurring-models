<?php

namespace MohammedManssour\LaravelRecurringModels\Support\PendingRepeats;

use Illuminate\Support\Collection;
use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Exceptions\RepetitionEndsAfterNotAvailableException;

class PendingEveryWeekRepeat extends PendingRepeat
{
    /**
     * days
     *
     * @var Collection<int, string>
     */
    private Collection $days;

    /** @var Collection<int, array<string, string>> */
    private Collection $rules;

    public function __construct(Repeatable $model)
    {
        parent::__construct($model);
        $this->days = collect([]);
        $this->rules = collect([]);
    }

    /**
     * Repeat every week on specific days
     *
     * @param  string[]  $days  acceptable = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday']
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

    /**
     * @return array<string, string>
     */
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
                    strtolower($this->model->repetitionBaseDate(RepetitionType::Complex)->format('l'))
                )
            );

            return;
        }

        $this->rules = $this->days->map(fn ($day) => $this->getRule($day));
    }

    /**
     * @return array<string, string>
     */
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

    public function __destruct()
    {
        $this->rules();
    }

    /**
     * @return string[]
     */
    private function weekdays(): array
    {
        return ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    }
}

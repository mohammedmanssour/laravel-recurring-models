<?php

namespace MohammedManssour\LaravelRecurringModels\Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Models\Repetition;

/**
 * @extends Factory<Repetition>
 */
class RepetitionFactory extends Factory
{
    protected $model = Repetition::class;

    public function definition()
    {
        return [
            'repeatable_id' => null,
            'repeatable_type' => null,
            'type' => RepetitionType::Simple,
            'start_at' => Carbon::make(fake()->dateTime())->startOfHour(),
            'interval' => $this->toSeconds(fake()->numberBetween(1, 30)),
            'year' => null,
            'month' => null,
            'day' => null,
            'week' => null,
            'weekday' => null,
            'week_of_month' => null,
            'end_at' => null,
        ];
    }

    public function morphs(Model $model): static
    {
        return $this->state([
            'repeatable_id' => $model->getKey(),
            'repeatable_type' => $model->getMorphClass(),
        ]);
    }

    public function complex(string $year = '*', string $month = '*', string $day = '*', string $week = '*', string $weekOfMonth = '*', string $weekday = '*'): static
    {
        return $this->state([
            'type' => RepetitionType::Complex,
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'week' => $week,
            'week_of_month' => $weekOfMonth,
            'weekday' => $weekday,
        ]);
    }

    public function interval(?int $days = null): static
    {
        return $this->state([
            'interval' => $this->toSeconds($days ?? fake()->numberBetween(1, 30)),
        ]);
    }

    public function starts(Carbon|string $date = 'now'): static
    {
        return $this->state([
            'start_at' => Carbon::make($date)->startOfHour(),
        ]);
    }

    public function ends(Carbon|string $date = 'now'): static
    {
        return $this->state([
            'end_at' => Carbon::make($date)->startOfHour(),
        ]);
    }

    private function toSeconds(int $days): int
    {
        return $days * 24 * 60 * 60;
    }
}

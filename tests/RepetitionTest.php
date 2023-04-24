<?php

use Illuminate\Support\Carbon;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Models\Repetition;
use MohammedManssour\LaravelRecurringModels\Tests\Stubs\Models\Task;

function repetition(Task $task, $end_at = null): Repetition
{
    $factory = Repetition::factory()
        ->morphs($task)
        ->starts('2023-04-15')
        ->interval(5);

    if ($end_at) {
        $factory = $factory->ends($end_at);
    }

    return $factory->create();
}

it('can check if simple repetition occurres on specific dates', function () {
    $repetition = repetition($this->task());

    $model = Repetition::whereOccurresOn(Carbon::make('2023-04-10 00:00:00'))->first();
    expect($model)->toBeNull();

    $model = Repetition::whereOccurresOn(Carbon::make('2023-04-20 00:00:00'))->first();
    expect($model->id)->toBe($repetition->id);

    $model = Repetition::whereOccurresOn(Carbon::make('2023-04-25 00:00:00'))->first();
    expect($model->id)->toBe($repetition->id);
});

it('can check if simple repetition occurres on specific dates after end date', function () {
    repetition($this->task(), '2023-04-23 00:00:00');

    $model = Repetition::whereOccurresOn(Carbon::make('2023-04-25 00:00:00'))->first();
    expect($model)->toBeNull();
});

it('can check if simple repetition occurres between two specific dates', function () {
    $repetition = repetition($this->task());

    $model = Repetition::whereOccurresBetween(
        Carbon::make('2023-04-20 00:00:00'),
        Carbon::make('2023-04-25 00:00:00'),
    )->first();
    expect($model->id)->toBe($repetition->id);
});

it('can check if complex repetition occurres on specific dates', function () {

    Carbon::setTestNow(
        Carbon::make('2023-04-20')
    );

    // repeats on second Friday of the month
    $repetition = Repetition::factory()->morphs($this->task())->complex(week: 2, weekday: Carbon::FRIDAY)->starts($this->task()->startsAt())->create();

    $model = Repetition::whereOccurresOn(Carbon::make('2023-05-12'))->first();
    expect($model)->not()->toBeNull();
    expect($model->type)->toBe(RepetitionType::Complex);
    expect($model->id)->toBe($repetition->id);

    $model = Repetition::whereOccurresOn(Carbon::make('2023-05-05'))->first();
    expect($model)->toBeNull();

    $model = Repetition::whereOccurresOn(Carbon::make('2023-05-19'))->first();
    expect($model)->toBeNull();
});

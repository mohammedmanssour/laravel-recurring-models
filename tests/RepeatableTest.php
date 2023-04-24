<?php

use Illuminate\Support\Carbon;
use MohammedManssour\LaravelRecurringModels\Models\Repetition;
use MohammedManssour\LaravelRecurringModels\Tests\Stubs\Models\Task;

it('can get the repeatable model that will occure in a specific date', function () {
    repetition($this->task(), '2023-04-23 00:00:00');

    $model = Task::whereOccurresOn(Carbon::make('2023-04-20 00:00:00'))->first();
    expect($model->id)->toBe($this->task()->id);

    $model = Repetition::whereOccurresOn(Carbon::make('2023-04-25 00:00:00'))->first();
    expect($model)->toBeNull();
});

it('can get the repeatable model that will occure between specific dates', function () {
    repetition($this->task(), '2023-04-23 00:00:00');

    $model = Task::whereOccurresBetween(
        Carbon::make('2023-04-20 00:00:00'),
        Carbon::make('2023-04-25 00:00:00'),
    )->first();
    expect($model->id)->toBe($this->task()->id);
});

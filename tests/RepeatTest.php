<?php

use Carbon\Carbon;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Exceptions\ComplexRepetionEndsAfterNotAvailableException;

it('can create daily repeatition for task with no end', function () {
    $this->task()
        ->repeat()
        ->everyNDays(5);

    $this->assertDatabaseHas('repetitions', [
        'start_at' => $this->task()->startsAt()->addDays(5),
        'interval' => 5 * 86400,
        'end_at' => null,
    ]);
});

it('can create daily repeatition for task that ends in a specific date', function () {
    $this->task()
        ->repeat()
        ->everyNDays(2)
        ->endsAt(Carbon::make('2023-04-25'));

    $this->assertDatabaseHas('repetitions', [
        'start_at' => $this->task()->startsAt()->addDays(2),
        'interval' => 2 * 86400,
        'end_at' => '2023-04-25 00:00:00',
    ]);
});

it('can create daily repeatition for task that ends after n times', function () {
    $this->task()
        ->repeat()
        ->everyNDays(3)
        ->endsAfter(5);

    $this->assertDatabaseHas('repetitions', [
        'start_at' => $this->task()->startsAt()->addDays(3),
        'interval' => 3 * 86400,
        'end_at' => $this->task()->startsAt()->addDays(18), // because five times means 5 repetitions
    ]);
});

it('can create daily repeatition for task', function () {
    $this->task()
        ->repeat()
        ->daily();

    $this->assertDatabaseHas('repetitions', [
        'start_at' => $this->task()->startsAt()->addDays(1),
        'interval' => 86400,
        'end_at' => null,
    ]);
});

it('can create weekly repetition for task', function () {
    $this->task()
        ->repeat()
        ->weekly();

    $this->assertDatabaseHas('repetitions', [
        'start_at' => $this->task()->startsAt()->addDays(7),
        'interval' => 7 * 86400,
        'end_at' => null,
    ]);

    $endAt = $this->task()->startsAt()->clone()->addDays(100);
    $this->task()
        ->repeat()
        ->weekly()
        ->endsAt($endAt);

    $this->assertDatabaseHas('repetitions', [
        'start_at' => $this->task()->startsAt()->addDays(7),
        'interval' => 7 * 86400,
        'end_at' => $endAt,
    ]);

    $this->task()
        ->repeat()
        ->weekly()
        ->endsAfter(10);

    $this->assertDatabaseHas('repetitions', [
        'start_at' => $this->task()->startsAt()->addDays(7),
        'interval' => 7 * 86400,
        'end_at' => $this->task()->startsAt()->addDays(77),
    ]);
});

it('can create weekly repeatition for task that occurres on specific days', function () {
    $days = ['sunday', 'tuesday', 'thursday'];
    $endAt = $this->task()->startsAt()->clone()->addDays(100);
    $this->task()
        ->repeat()
        ->weekly()
        ->on($days)
        ->endsAt($endAt);

    foreach ($days as $day) {
        $this->assertDatabaseHas('repetitions', [
            'start_at' => $this->task()->startsAt()->next($day),
            'interval' => 7 * 86400,
            'end_at' => $endAt,
        ]);
    }
});

it('can create weekly repetition for task that occurres on specific days and ends after n times', function () {

    $days = ['sunday', 'tuesday', 'thursday'];

    $this->task()
        ->repeat()
        ->weekly()
        ->on($days)
        ->endsAfter(5);

    $endAt = $this->task()->startsAt()->next('sunday')->next('tuesday')->next('tuesday');

    foreach ($days as $day) {
        $this->assertDatabaseHas('repetitions', [
            'start_at' => $this->task()->startsAt()->next($day),
            'interval' => 7 * 86400,
            'end_at' => $endAt,
        ]);
    }
});

it('can create complex repetition patterns for task', function () {
    $this->task()
        ->repeat()
        ->complex(week: 2, weekday: Carbon::FRIDAY);

    $this->assertDatabaseHas('repetitions', [
        'type' => RepetitionType::Complex,
        'start_at' => $this->task()->startsAt()->clone()->addDay(),
        'interval' => null,
        'year' => '*',
        'month' => '*',
        'day' => '*',
        'week' => 2,
        'weekday' => Carbon::FRIDAY,
    ]);
});

it('can explicitly set start_at date', function () {
    $this->task()
        ->repeat()
        ->daily()
        ->startsAt(Carbon::make('2023-05-01'));

    $this->assertDatabaseHas('repetitions', [
        'start_at' => '2023-05-01 00:00:00',
    ]);
});

it('will throw an exception when using endsAfter with complex repetitions patterns', function () {
    $this->expectException(ComplexRepetionEndsAfterNotAvailableException::class);

    // repeat task on the second week of every month of the year 2023
    $this->task()
        ->repeat()
        ->complex(year: 2023, week: 2)
        ->endsAfter(3);
});

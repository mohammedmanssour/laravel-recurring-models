<?php

use Carbon\Carbon;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Exceptions\RepetitionEndsAfterNotAvailableException;
use MohammedManssour\LaravelRecurringModels\Tests\Stubs\Support\HasTask;
use MohammedManssour\LaravelRecurringModels\Tests\TestCase;

class RepeatTest extends TestCase
{
    use HasTask;

    /** @test */
    public function it_creates_daily_repetition_for_task_with_no_end()
    {
        $this->task()
            ->repeat()
            ->everyNDays(5);

        $this->assertDatabaseHas('repetitions', [
            'start_at' => $this->task()->repetitionBaseDate()->addDays(5),
            'interval' => 5 * 86400,
            'end_at' => null,
        ]);
    }

    /** @test */
    public function it_creates_daily_repetition_for_task_that_ends_in_a_specific_date()
    {
        $this->task()
            ->repeat()
            ->everyNDays(2)
            ->endsAt(Carbon::make('2023-04-25'));

        $this->assertDatabaseHas('repetitions', [
            'start_at' => $this->task()->repetitionBaseDate()->addDays(2),
            'interval' => 2 * 86400,
            'end_at' => '2023-04-25 00:00:00',
        ]);
    }

    /** @test */
    public function it_can_create_daily_repetition_for_task_that_ends_after_n_times()
    {
        $this->task()
            ->repeat()
            ->everyNDays(3)
            ->endsAfter(5);

        $this->assertDatabaseHas('repetitions', [
            'start_at' => $this->task()->repetitionBaseDate()->addDays(3),
            'interval' => 3 * 86400,
            'end_at' => $this->task()->repetitionBaseDate()->addDays(18), // because five times means 5 repetitions
        ]);
    }

    /** @test */
    public function it_create_daily_repetition()
    {
        $this->task()
            ->repeat()
            ->daily();

        $this->assertDatabaseHas('repetitions', [
            'start_at' => $this->task()->repetitionBaseDate()->addDays(1),
            'interval' => 86400,
            'end_at' => null,
        ]);
    }

    /** @test */
    public function it_creates_weekly_repetition()
    {
        $this->task()
            ->repeat()
            ->weekly();

        $this->assertEquals(1, $this->task()->repetitions()->count());

        $this->assertDatabaseHas('repetitions', [
            'start_at' => $this->task()->repetitionBaseDate()->addDay(),
            'type' => 'complex',
            'weekday' => $this->task()->repetitionBaseDate()->weekday(),
            'end_at' => null,
        ]);
    }

    /** @test */
    public function it_creates_weekly_repetition_for_task_that_occrres_on_specific_days()
    {
        $days = ['sunday', 'tuesday', 'thursday'];
        $endAt = $this->task()->repetitionBaseDate()->clone()->addDays(100);
        $this->task()
            ->repeat()
            ->weekly()
            ->on($days)
            ->endsAt($endAt);

        foreach ([0, 2, 4] as $day) {
            $this->assertDatabaseHas('repetitions', [
                'start_at' => $this->task()->repetitionBaseDate()->addDay(),
                'type' => 'complex',
                'weekday' => $day,
                'end_at' => $endAt,
            ]);
        }
    }

    /** @test */
    public function it_creates_complex_repetition_patterns_for_task()
    {
        $this->task()
            ->repeat()
            ->complex(week: 2, weekday: Carbon::FRIDAY);

        $this->assertDatabaseHas('repetitions', [
            'type' => RepetitionType::Complex,
            'start_at' => $this->task()->repetitionBaseDate()->clone()->addDay(),
            'interval' => null,
            'year' => '*',
            'month' => '*',
            'day' => '*',
            'week' => 2,
            'weekday' => Carbon::FRIDAY,
        ]);
    }

    /** @test */
    public function it_sets_start_date_explicitly()
    {
        $this->task()
            ->repeat()
            ->daily()
            ->startsAt(Carbon::make('2023-05-01'));

        $this->assertDatabaseHas('repetitions', [
            'start_at' => '2023-05-01 00:00:00',
        ]);
    }

    /** @test */
    public function it_throws_an_exception_when_using_endsAfter_with_complex_repeitions()
    {
        $this->expectException(RepetitionEndsAfterNotAvailableException::class);

        // repeat task on the second week of every month of the year 2023
        $this->task()
            ->repeat()
            ->complex(year: 2023, week: 2)
            ->endsAfter(3);
    }
}

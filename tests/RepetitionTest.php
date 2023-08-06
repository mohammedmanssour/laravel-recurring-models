<?php

use Illuminate\Support\Carbon;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Models\Repetition;
use MohammedManssour\LaravelRecurringModels\Tests\Stubs\Support\HasTask;
use MohammedManssour\LaravelRecurringModels\Tests\TestCase;

class RepetitionTest extends TestCase
{
    use HasTask;

    /** @test */
    public function it_checks_if_simple_repetition_occurres_on_specific_day()
    {
        // repeat start at 2023-04-15 00:00:00
        $repetition = $this->repetition($this->task());

        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-10 00:00:00'))->first();
        $this->assertNull($model);

        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-20 23:00:00'))->first();
        $this->assertTrue($repetition->is($model));

        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-16 23:00:00'))->first();
        $this->assertFalse($repetition->is($model));

        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-17 23:00:00'))->first();
        $this->assertFalse($repetition->is($model));

        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-18 23:00:00'))->first();
        $this->assertFalse($repetition->is($model));

        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-19 23:00:00'))->first();
        $this->assertFalse($repetition->is($model));

        // ensure the day no matter what the hour is. usefull when handling timezones
        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-20 23:00:00'))->first();
        $this->assertTrue($repetition->is($model));

        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-25 00:00:00'))->first();
        $this->assertTrue($repetition->is($model));
    }

    /** @test */
    public function it_checks_if_simple_repetition_occurres_on_specific_dates_after_end_date()
    {
        $this->repetition($this->task(), '2023-04-23 00:00:00');

        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-25 00:00:00'))->first();
        $this->assertNull($model);
    }

    /** @test */
    public function it_checks_if_simple_repetition_occurres_between_two_specific_dates()
    {
        // repeat start at 2023-04-15 00:00:00
        $repetition = $this->repetition($this->task());

        $model = Repetition::whereOccurresBetween(
            Carbon::make('2023-04-20 00:00:00'),
            Carbon::make('2023-04-25 00:00:00'),
        )->first();
        $this->assertTrue($repetition->is($model));

        $this->assertFalse(
            Repetition::whereOccurresBetween(
                Carbon::make('2023-04-10 00:00:00'),
                Carbon::make('2023-04-14 00:00:00'),
            )
                ->exists()
        );
    }

    /** @test */
    public function it_checks_if_complex_repetition_occurres_on_specific_dates()
    {
        Carbon::setTestNow(
            Carbon::make('2023-04-20')
        );

        // repeats on second Friday of the month
        $repetition = Repetition::factory()->morphs($this->task())->complex(week: 2, weekday: Carbon::FRIDAY)->starts($this->task()->repetitionBaseDate())->create();

        $model = Repetition::whereOccurresOn(Carbon::make('2023-05-12'))->first();
        $this->assertNotNull($model);
        $this->assertEquals(RepetitionType::Complex, $model->type);
        $this->assertEquals($repetition->id, $model->id);

        $model = Repetition::whereOccurresOn(Carbon::make('2023-05-05'))->first();
        $this->assertNull($model);

        $model = Repetition::whereOccurresOn(Carbon::make('2023-05-19'))->first();
        $this->assertNull($model);
    }
}

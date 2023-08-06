<?php

use Illuminate\Support\Carbon;
use MohammedManssour\LaravelRecurringModels\Models\Repetition;
use MohammedManssour\LaravelRecurringModels\Tests\Stubs\Models\Task;
use MohammedManssour\LaravelRecurringModels\Tests\Stubs\Support\HasTask;
use MohammedManssour\LaravelRecurringModels\Tests\TestCase;

class RepeatableTest extends TestCase
{
    use HasTask;

    /** @test */
    public function it_gets_the_repeatable_model_that_will_occure_in_a_specific_date()
    {
        $this->repetition($this->task(), '2023-04-23 00:00:00');

        $model = Task::whereOccurresOn(Carbon::make('2023-04-20 00:00:00'))->first();
        $this->assertTrue($this->task()->is($model));

        $model = Repetition::whereOccurresOn(Carbon::make('2023-04-25 00:00:00'))->first();
        $this->assertNull($model);
    }

    /** @test */
    public function it_gets_the_repeatable_model_that_will_occure_between_specific_dates()
    {
        $this->repetition($this->task(), '2023-04-23 00:00:00');

        $model = Task::whereOccurresBetween(
            Carbon::make('2023-04-20 00:00:00'),
            Carbon::make('2023-04-25 00:00:00'),
        )->first();
        $this->assertTrue($this->task()->is($model));
    }
}

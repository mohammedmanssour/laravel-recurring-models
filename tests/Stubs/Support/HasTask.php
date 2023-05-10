<?php

namespace MohammedManssour\LaravelRecurringModels\Tests\Stubs\Support;

use MohammedManssour\LaravelRecurringModels\Models\Repetition;
use MohammedManssour\LaravelRecurringModels\Tests\Stubs\Models\Task;

trait HasTask
{
    protected Task $task;

    public function task(): Task
    {
        if (! isset($this->task)) {
            $this->task = Task::create(['title' => fake()->words(asText: true)]);
        }

        return $this->task;
    }

    public function repetition(Task $task, $end_at = null)
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
}

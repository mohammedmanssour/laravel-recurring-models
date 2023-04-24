<?php

namespace MohammedManssour\LaravelRecurringModels\Tests\Stubs\Support;

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
}

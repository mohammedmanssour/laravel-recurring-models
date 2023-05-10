<?php

namespace MohammedManssour\LaravelRecurringModels\Tests\Stubs\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use MohammedManssour\LaravelRecurringModels\Concerns\Repeatable;
use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable as RepeatableContract;

class Task extends Model implements RepeatableContract
{
    use Repeatable;

    protected $fillable = ['title'];

    public $timestamps = false;

    /**
     * define the base date that we would use to calculate repetition start_at
     */
    public function repetitionBaseDate(): Carbon
    {
        return now();
    }
}

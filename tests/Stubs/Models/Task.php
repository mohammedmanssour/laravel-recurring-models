<?php

namespace MohammedManssour\LaravelRecurringModels\Tests\Stubs\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable as RepeatableContract;
use MohammedManssour\LaravelRecurringModels\Support\Repeatable;

class Task extends Model implements RepeatableContract
{
    use Repeatable;

    protected $fillable = ['title'];

    public $timestamps = false;

    /**
     * define the base date that we would use to calculate repetition start_at
     */
    public function startsAt(): Carbon
    {
        return now();
    }
}

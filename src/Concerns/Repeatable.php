<?php

namespace MohammedManssour\LaravelRecurringModels\Concerns;

use Carbon\CarbonInterface as Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Models\Repetition;
use MohammedManssour\LaravelRecurringModels\Support\Repeat;

/**
 * @property-read \Illuminate\Support\Collection<integer, \Modules\RecurringEvents\Entities\Repeat> $repeats
 */
trait Repeatable
{
    /*-----------------------------------------------------
    * Relations
    -----------------------------------------------------*/
    public function repetitions(): MorphMany
    {
        return $this->morphMany(Repetition::class, 'repeatable');
    }

    /*-----------------------------------------------------
    * Methods
    -----------------------------------------------------*/
    /**
     * define the base date that we would use to calculate repetition start_at
     */
    public function repetitionBaseDate(RepetitionType $type = null): Carbon
    {
        return $this->created_at;
    }

    /**
     * initiate model repeat specs
     */
    public function repeat(): Repeat
    {
        return new Repeat($this);
    }

    /*-----------------------------------------------------
    * Scopes
    -----------------------------------------------------*/
    public function scopeWhereOccurresOn(Builder $query, Carbon $date): Builder
    {
        return $query->whereHas(
            'repetitions',
            fn ($repetitions) => $repetitions->whereOccurresOn($date)
        );
    }

    public function scopeWhereOccurresBetween(Builder $query, Carbon $start, Carbon $end): Builder
    {
        return $query->whereHas(
            'repetitions',
            fn ($repetitions) => $repetitions->whereOccurresBetween($start, $end)
        );
    }
}

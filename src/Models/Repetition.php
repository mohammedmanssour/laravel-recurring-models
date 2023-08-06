<?php

namespace MohammedManssour\LaravelRecurringModels\Models;

use Carbon\CarbonInterface as Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use MohammedManssour\LaravelRecurringModels\Database\Factories\RepetitionFactory;
use MohammedManssour\LaravelRecurringModels\Enums\RepetitionType;
use MohammedManssour\LaravelRecurringModels\Exceptions\DriverNotSupportedException;
use MohammedManssour\LaravelRecurringModels\Support\RepeatCollection;

/**
 * @model Repetition
 *
 * @property int $id
 * @property \MohammedManssour\LaravelRecurringModels\Enums\RepetitionType $type
 * @property \Carbon\Carbon $start_at
 * @property ?int $interval
 * @property ?\Carbon\Carbon $end_at
 */
class Repetition extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'start_at', 'interval', 'end_at',
        'year', 'month', 'day', 'week', 'weekday',
    ];

    protected $casts = [
        'type' => RepetitionType::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    protected static function newFactory()
    {
        return RepetitionFactory::new();
    }

    public function newCollection(array $models = []): RepeatCollection
    {
        return new RepeatCollection($models);
    }

    /*-----------------------------------------------------
    * Relations
    -----------------------------------------------------*/
    public function repeatable(): MorphTo
    {
        return $this->morphTo('repeatable');
    }

    /*-----------------------------------------------------
    * scopes
    -----------------------------------------------------*/
    public function scopeWhereOccurresOn(Builder $query, Carbon $date): Builder
    {
        return $query->where('start_at', '<=', $date->toDateTimeString())
            ->where(
                fn ($query) => $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', $date)
            )
            ->where(
                fn ($query) => $query->where(fn ($query) => $this->simpleQuery($query, $date))
                    ->orWhere(fn ($query) => $this->complexQuery($query, $date))
            );
    }

    public function scopeWhereOccurresBetween(Builder $query, Carbon $start, Carbon $end): Builder
    {
        $dates = CarbonPeriod::create(
            $start,
            $end,
        );

        foreach ($dates as $date) {
            $query->orWhere(fn ($query) => $query->whereOccurresOn($date));
        }

        return $query;
    }

    private function simpleQuery(Builder $query, Carbon $date): Builder
    {
        $secondsInDay = 86400;

        $query
            ->where('type', RepetitionType::Simple);

        $driver = $query->getConnection()->getConfig('driver');
        match ($driver) {
            'mysql' => $query->whereRaw('(UNIX_TIMESTAMP(?)  - UNIX_TIMESTAMP(`start_at`)) % `interval` BETWEEN 0 AND ?', [$date->toDateTimeString(), $secondsInDay]),
            'sqlite' => $query->whereRaw('(? - unixepoch(`start_at`)) % `interval` BETWEEN 0 AND ?', [$date->timestamp, $secondsInDay]),
            'pgsql' => $query->whereRaw("MOD((? - DATE_PART('EPOCH', start_at))::INTEGER, interval) BETWEEN 0 AND ?", [$date->timestamp, $secondsInDay]),
            default => throw new DriverNotSupportedException($driver),
        };

        return $query;
    }

    private function complexQuery(Builder $query, Carbon $date): Builder
    {
        return $query->where('type', RepetitionType::Complex)
            ->where(fn ($query) => $query->where('year', '*')->orWhere('year', $date->year))
            ->where(fn ($query) => $query->where('month', '*')->orWhere('month', $date->month))
            ->where(fn ($query) => $query->where('day', '*')->orWhere('day', $date->day))
            ->where(fn ($query) => $query->where('week', '*')->orWhere('week', $date->weekOfMonth))
            ->where(fn ($query) => $query->where('weekday', '*')->orWhere('weekday', $date->dayOfWeek));
    }
}

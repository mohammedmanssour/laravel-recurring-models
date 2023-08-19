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
        'start_at', 'tz_offset', 'interval', 'end_at',
        'year', 'month', 'day', 'week', 'week_of_month', 'weekday',
    ];

    protected $casts = [
        'type' => RepetitionType::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'tz_offset' => 'integer',
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
    public function scopeWhereActiveForTheDate(Builder $query, Carbon $date): Builder
    {
        return $query->where('start_at', '<=', $date->toDateTimeString())
            ->where(
                fn ($query) => $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', $date)
            );
    }

    public function scopeWhereOccurresOn(Builder $query, Carbon $date): Builder
    {
        return $query
            ->WhereActiveForTheDate($date)
            ->where(
                fn ($query) => $query->whereHasSimpleRecurringOn($date)
                    ->orWhere(fn ($query) => $query->whereHasComplexRecurringOn($date))
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

    public function scopeWhereHasSimpleRecurringOn(Builder $query, Carbon $date): Builder
    {
        $secondsInDay = 86399;
        $timestamp = $date->clone()->utc()->timestamp;

        $query
            ->where('type', RepetitionType::Simple);

        $driver = $query->getConnection()->getConfig('driver');
        match ($driver) {
            'mysql' => $query->whereRaw('(?  - UNIX_TIMESTAMP(`start_at`)) % `interval` BETWEEN 0 AND ?', [$timestamp, $secondsInDay]),
            'sqlite' => $query->whereRaw('(? - unixepoch(`start_at`)) % `interval` BETWEEN 0 AND ?', [$timestamp, $secondsInDay]),
            'pgsql' => $query->whereRaw("MOD((? - DATE_PART('EPOCH', start_at))::INTEGER, interval) BETWEEN 0 AND ?", [$timestamp, $secondsInDay]),
            default => throw new DriverNotSupportedException($driver),
        };

        return $query;
    }

    public function scopeWhereHasComplexRecurringOn(Builder $query, Carbon $date)
    {
        $query->where('type', RepetitionType::Complex)
            ->where(fn ($query) => $query->where('year', '*')->orWhere('year', $date->year))
            ->where(fn ($query) => $query->where('month', '*')->orWhere('month', $date->month))
            ->where(fn ($query) => $query->where('day', '*')->orWhere('day', $date->day))
            ->where(fn ($query) => $query->where('week', '*')->orWhere('week', $date->week))
            ->where(fn ($query) => $query->where('week_of_month', '*')->orWhere('week_of_month', $date->weekOfMonth))
            ->where(fn ($query) => $query->where('weekday', '*')->orWhere('weekday', $date->dayOfWeek));
    }
}

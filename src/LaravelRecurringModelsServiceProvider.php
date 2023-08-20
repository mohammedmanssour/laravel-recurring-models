<?php

namespace MohammedManssour\LaravelRecurringModels;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRecurringModelsServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        parent::boot();

        if ($this->app->environment('testing')) {
            $this->loadMigrationsFrom(__DIR__.'/../tests/Stubs/Migrations');
        }
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-recurring-models')
            ->hasMigration('1682348400_create_recurring_models_table')
            ->hasMigration('1692297663_add_tz_offset_to_repetitions_table')
            ->hasMigration('1692434186_adds_week_of_month_to_repetitions_table')
            ->runsMigrations($this->app->environment('testing'));
    }
}

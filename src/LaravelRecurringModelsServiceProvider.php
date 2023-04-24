<?php

namespace MohammedManssour\LaravelRecurringModels;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelRecurringModelsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-recurring-models')
            ->hasMigration('create_recurring_models_table');
    }
}

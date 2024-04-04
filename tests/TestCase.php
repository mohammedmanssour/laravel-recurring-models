<?php

namespace MohammedManssour\LaravelRecurringModels\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use MohammedManssour\LaravelRecurringModels\LaravelRecurringModelsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    const NOW = '2023-04-21 00:00:00';

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MohammedManssour\\LaravelRecurringModels\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        Carbon::setTestNow(self::NOW);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelRecurringModelsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);

        $app['config']->set('database.default', 'testing');

        $migrations = [
            __DIR__.'/../database/migrations/1682348400_create_recurring_models_table.php',
            __DIR__.'/../database/migrations/1692297663_add_tz_offset_to_repetitions_table.php',
            __DIR__.'/../database/migrations/1692434186_adds_week_of_month_to_repetitions_table.php',
            __DIR__.'/Stubs/Migrations/2023_04_18_000000_create_tasks_table.php',
        ];

        foreach ($migrations as $migrationPath) {
            $migration = include $migrationPath;
            $migration->up();
        }

        // $app['config']->set('database.connections.mysql', [
        //     'driver' => 'mysql',
        //     'host' => env('MYSQL_DB_HOST', '127.0.0.1'),
        //     'port' => env('MYSQL_DB_PORT', '3306'),
        //     'database' => env('MYSQL_DB_DATABASE', 'forge'),
        //     'username' => env('MYSQL_DB_USERNAME', 'forge'),
        //     'password' => env('MYSQL_DB_PASSWORD', ''),
        // ]);

        // $app['config']->set('database.connections.pgsql', [
        //     'driver' => 'pgsql',
        //     'host' => env('PQSQL_DB_HOST', '127.0.0.1'),
        //     'port' => env('PQSQL_DB_PORT', '3306'),
        //     'database' => env('PQSQL_DB_DATABASE', 'forge'),
        //     'username' => env('PQSQL_DB_USERNAME', 'forge'),
        //     'password' => env('PQSQL_DB_PASSWORD', ''),
        // ]);
    }
}

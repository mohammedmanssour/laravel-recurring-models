<?php

namespace MohammedManssour\LaravelRecurringModels\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Support\Carbon;
use MohammedManssour\LaravelRecurringModels\LaravelRecurringModelsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MohammedManssour\\LaravelRecurringModels\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        Carbon::setTestNow('2023-04-21');
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

        $tasksMigration = include __DIR__.'/./Stubs/Migrations/2023_04_18_000000_create_tasks_table.php';
        $tasksMigration->down();
        $tasksMigration->up();

        $repetitionsMigration = include __DIR__.'/../database/migrations/create_recurring_models_table.php';
        $repetitionsMigration->down();
        $repetitionsMigration->up();
    }
}

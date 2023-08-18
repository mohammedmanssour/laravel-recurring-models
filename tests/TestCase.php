<?php

namespace MohammedManssour\LaravelRecurringModels\Tests;

use Illuminate\Support\Carbon;
use Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use MohammedManssour\LaravelRecurringModels\LaravelRecurringModelsServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    const NOW = '2023-04-21 00:00:00';

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'MohammedManssour\\LaravelRecurringModels\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        Carbon::setTestNow(self::NOW);
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Stubs/Migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelRecurringModelsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app->useEnvironmentPath(__DIR__ . '/..');
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
    }
}

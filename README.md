# Laravel Recurring Models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mohammedmanssour/laravel-recurring-models.svg?style=flat-square)](https://packagist.org/packages/mohammedmanssour/laravel-recurring-models)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mohammedmanssour/laravel-recurring-models/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/mohammedmanssour/laravel-recurring-models/actions?query=workflow%3Arun-tests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mohammedmanssour/laravel-recurring-models/fix-php-code-style-issues.yml?branch=master&label=code%20style&style=flat-square)](https://github.com/mohammedmanssour/laravel-recurring-models/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/mohammedmanssour/laravel-recurring-models.svg?style=flat-square)](https://packagist.org/packages/mohammedmanssour/laravel-recurring-models)

Introducing our Laravel Recurring package - the ultimate solution for adding recurring functionality to your Laravel Models! Whether you need simple daily, weekly, or every n days recurrence, or more complex patterns like repeating a model on the second Friday of every month, our package has got you covered. With a seamless integration into your Laravel application, you can easily manage and automate recurring tasks with just a few lines of code.

## Installation

You can install the package via composer:

```bash
composer require mohammedmanssour/laravel-recurring-models
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="recurring-models-migrations"
php artisan migrate
```

## Usage

### Adding the recurring functionality to Models:

1. Make sure you models implements `Repeatable` Contract.

```php
use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable as RepeatableContract;

class Task extends Model implements RepeatableContract
{

}
```

2. Add the `Repeatable` trait to your Model

```php
use MohammedManssour\LaravelRecurringModels\Contracts\Repeatable as RepeatableContract;
use MohammedManssour\LaravelRecurringModels\Concerns\Repeatable;

class Task extends Model implements RepeatableContract
{
    use Repeatable;
}
```

3. Optionaly, customize Model base date.

```php
/**
 * define the base date that we would use to calculate repetition start_at
 */
public function repetitionBaseDate(): Carbon
{
    return $this->created_at;
}
```

### Model Recurring rules

The `Repeatable` trait has `repeat` method that will help you define the recurrence rules for the model.

The `repeat` method will return `Repeat` helper class that has 4 methods: `daily`, `everyNDays`, `weekly`, and `complex`

#### 1. Daily Recurrence

This will help you to create daily recurring rule for the model.

```php
$model->repeat()->daily()
```

The recurrence will start the next day based on the `repetitionBaseDate` returned value.

#### 2. Every N Days Recurrence

This will help you to create every N Days recurring rules for the model.

```php
$model->repeat()->everyNDays(days: 3)
```

The recurrence will start after n=3 days based on the `repetitionBaseDate` returned value.

#### 3. Weekly Recurrence

This will help ypi create weekly recurrence rule for the model.

```php
$model->repeat()->weekly()
```

The recurrence will start after 7 days based on the `repetitionBaseDate` returned value.

You can specify the days of the recurrence using the `on` method.

```php
$model->repeat()->weekly()->on(['sunday', 'monday', 'tuesday'])
```

#### 4. Complex Recurrence.

This will help you create complex recurrence rules for the task.

```php
$model->repeat()
    ->complex(
        year: '*',
        month: '*',
        day: '*',
        week: '*',
        weekday: '*'
    )
```

##### Examples

1. Repeat model on the second friday of every month.

```php
$model->repeat()->complex(week: 2, weekday: Carbon::FRIDAY)
```

2. Repeat model on the 15th day of every month.

```php
$model->repeat()->complex(day: 15)
```

### Model Scopes

use `whereOccurresOn` scope to get models that occurres on a specific date.

```
Task::whereOccurresOn(Carbon::make('2023-05-01'))->get()
```

use `whereOccurresBetween` scope to get the models that occurres between two sepcific dates.

```
Task::whereOccurresBetweeb(Carbon::make('2023-05-01'), Carbon::make('2023-05-30'))->get()
```

#### 1. End Recurrance

use `endsAt` to end occurrance on a specific date

```php
$model->repeat()->daily()->endsAt(
    Carbon::make('2023-06-01')
)
```

use `endsAfter` to end occurrance after n times.

```php
$model->repeat()->daily()->endsAfter($times);
```

#### 2. Start Recurrance

use `startsAt` method to start occurrance after a specific date.

```php
$model->repeat()->daily()->startsAt(
    Carbon::make()
)
```

## Testing

```bash
composer test
```

## Credits

-   [Mohammed Manssour](https://github.com/mohammedmanssour)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

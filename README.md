# Schedule artisan commands to run at a sub-minute frequency

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-short-schedule.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-short-schedule)
![Tests](https://github.com/spatie/laravel-short-schedule/workflows/Tests/badge.svg)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-short-schedule.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-short-schedule)

[Laravel's native scheduler](https://laravel.com/docs/master/scheduling) allows you to schedule Artisan commands to run every minute. 

If you need to execute something with a higher frequency, for example every second, than you've come to the right package. With laravel-short-schedule installed, you can do this:

```php
// in app\Console\Kernel.php

protected function shortSchedule(\Spatie\ShortSchedule\ShortSchedule $shortSchedule)
{
    // this command will run every second
    $shortSchedule->command('artisan-command')->everySecond();
    
    // this command will run every 30 seconds
    $shortSchedule->command('another-artisan-command')->everySeconds(30);
    
    // this command will run every half a second
    $shortSchedule->command('another-artisan-command')->everySeconds(0.5);
}
```

## Are you a visual learner?

In [this video](https://spatie.be/videos/laravel-package-training/laravel-short-schedule-part-1-using-the-package) you'll see a demonstration of the package. 

Want to know how it works under the hood? Then watch [this video](https://spatie.be/videos/laravel-package-training/laravel-short-schedule-part-2-under-the-hood).

Finally, there's [this video](https://spatie.be/videos/laravel-package-training/laravel-short-schedule-part-3-testing-the-package) that shows how the package is tested. You'll learn how you can test [ReactPHP](https://reactphp.org) powered loops.

These videos are also part of the [Laravel Package Training](https://laravelpackage.training).

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-short-schedule.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-short-schedule)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-short-schedule
```

In your production environment you can start the short scheduler with this command

```bash
php artisan short-schedule:run
```

You should use a process monitor like [Supervisor](http://supervisord.org/index.html) to keep this task going at all times, and to automatically start it when your server boots. Whenever you change the schedule, you should restart this command.

## Handle memory leaks

To deal with commands that leak memory, you can set the lifetime in seconds of the short schedule worker:

```bash
php artisan short-schedule:run --lifetime=60 // after 1 minute the worker will be terminated
```

After the given amount of seconds, the worker and all it's child processes will be terminated, freeing all memory. Then supervisor (or similar watcher) will bring it back.

### Lumen

Before you can run the `php artisan short-schedule:run` command in your Lumen project, you should make a copy of the `ShortScheduleRunCommand` into your `app/Commands` folder:

```bash
cp ./vendor/spatie/laravel-short-schedule/src/Commands/ShortScheduleRunCommand.php ./app/Console/Commands/ShortScheduleRunCommand.php
```

Next, edit the new `ShortScheduleRunCommand.php` file, and change the namespace from `namespace Spatie\ShortSchedule\Commands;` to `namespace App\Console\Commands;` and you're good to go!

## Usage

In `app\Console\Kernel` you should add a method named `shortSchedule`.

```php
// in app\Console\Kernel.php

protected function shortSchedule(\Spatie\ShortSchedule\ShortSchedule $shortSchedule)
{
    // this artisan command will run every second
    $shortSchedule->command('artisan-command')->everySecond();
}
```

### Specify the amount of seconds

You can run an artisan command every single second like this:

```php
$shortSchedule->command('artisan-command')->everySecond();
```

You can specify a specific amount of seconds using `everySeconds`

```php
$shortSchedule->command('artisan-command')->everySeconds(30);
```

You can even schedule tasks at sub-second frequency. This task will run every half a second.

```php
$shortSchedule->command('artisan-command')->everySeconds(0.5);
```

 ### Scheduling shell commands
 
 Use `exec` to schedule a bash command.
 
```php
$shortSchedule->exec('bash-command')->everySecond();
```
 
 ### Preventing overlaps
 
 By default, a scheduled command will run, even if the previous invocation is still running.
 
 You can prevent that by tacking on `withoutOverlapping`
 
```php
$shortSchedule->command('artisan-command')->everySecond()->withoutOverlapping();
```
 
 ### Between time constraints
 
 Limit the task to run between start and end times.
 
 ```php
 $shortSchedule->command('artisan-command')->between('09:00', '17:00')->everySecond();
 ```

It is safe use overflow days. In this example the command will run on every second between 21:00 and 01:00

 ```php
 $shortSchedule->command('artisan-command')->between('21:00', '01:00')->everySecond();
 ```
 
 ### Truth test constraints
 
 The command will run if the given closure return a truthy value. The closure will be evaluated at the same frequency the command is scheduled. So if you schedule the command to run every second, the given closure will also run every second.
 
```php
$shortSchedule->command('artisan-command')->when(fn() => rand() %2)->everySecond();
```

 ### Environment constraints
 
 The command will only run on the given environment.
 
 ```php
 $shortSchedule->command('artisan-command')->environment('production')->everySecond();
 ```

You can also pass an array:

 ```php
 $shortSchedule->command('artisan-command')->environment(['staging', 'production'])->everySecond();
 ```

### Composite constraints

You can use all constraints mentioned above at once. The command will only execute if all the used constraints pass.

 ```php
 $shortSchedule
   ->command('artisan-command')
   ->between('09:00', '17:00')
   ->when($callable)
   ->everySecond();
 ```

### Maintenance Mode

Commands won't run whilst Laravel is in maintenance mode. If you would like to force a command to run in maintenance mode you can use the `runInMaintenanceMode` method. 

```php
$shortSchedule->command('artisan-command')->everySecond()->runInMaintenanceMode();
```

### Running Tasks On One Server

Limit commands to only run on one server at a time. 

```php
$shortSchedule->command('artisan-command')->everySecond()->onOneServer();
```

## Events

Executing any code when responding to these events is blocking. If your code takes a long time to execute, all short scheduled jobs will be delayed. We highly recommend to put any code you wish to execute in response to these events on a queue. 

#### `Spatie\ShortSchedule\Events\ShortScheduledTaskStarting`

This event will be fired right before a task will be started. It has these public properties:

- `command`: the command string that will be executed
- `process`: the instance of `Symfony\Component\Process\Process` that will be used to execute the command

#### `Spatie\ShortSchedule\Events\ShortScheduledTaskStarted`

This event will be fired right before a task has been started. It has these public properties:

- `command`: the command string that is being executed
- `process`: the instance of `Symfony\Component\Process\Process` that is executing the command

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

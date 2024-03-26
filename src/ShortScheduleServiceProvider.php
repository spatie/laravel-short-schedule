<?php

namespace Spatie\ShortSchedule;

use React\EventLoop\Loop;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\ShortSchedule\Commands\ShortScheduleRunCommand;

class ShortScheduleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-short-schedule')
            ->hasCommand(ShortScheduleRunCommand::class);
    }

    public function bootingPackage()
    {
        $this->app->singleton(
            ShortSchedule::class,
            function () {
                $loop = Loop::get();

                return new ShortSchedule($loop);
            }
        );
    }
}

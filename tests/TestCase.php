<?php

namespace Spatie\ShortSchedule\Tests;

use Illuminate\Contracts\Console\Kernel;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\ShortSchedule\ShortScheduleServiceProvider;
use Spatie\ShortSchedule\Tests\TestClasses\TestKernel;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ShortScheduleServiceProvider::class,
        ];
    }

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton(Kernel::class, TestKernel::class);
    }
}

<?php

namespace Spatie\ShortSchedule\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\ShortSchedule\ShortScheduleServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ShortScheduleServiceProvider::class,
        ];
    }
}

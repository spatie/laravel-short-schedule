<?php

namespace Spatie\ShortSchedule\Tests\TestClasses;

use Closure;
use Illuminate\Foundation\Console\Kernel;
use Spatie\ShortSchedule\ShortSchedule;

class TestKernel extends Kernel
{
    protected static array $registeredShortScheduleCommands = [];

    protected function shortSchedule(ShortSchedule $shortSchedule)
    {
        collect(static::$registeredShortScheduleCommands)
            ->each(fn (Closure $closure) => $closure($shortSchedule));
    }

    public static function registerShortScheduleCommand(Closure $closure)
    {
        static::$registeredShortScheduleCommands[] = $closure;
    }

    public static function clearShortScheduleCommands()
    {
        static::$registeredShortScheduleCommands = [];
    }
}

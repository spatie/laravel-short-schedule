<?php

namespace Spatie\ShortSchedule\Tests\TestClasses;

use Orchestra\Testbench\Console\Kernel;
use Spatie\ShortSchedule\ShortSchedule;

class TestKernel extends Kernel
{
    protected function shortSchedule(ShortSchedule $shortSchedule)
    {
        $tempPath = __DIR__ . '/../temp';

        $file = "{$tempPath}/file.txt";

        $shortSchedule->exec("touch '{$file}'")->everySecond();
    }
}

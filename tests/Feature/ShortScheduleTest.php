<?php

namespace Spatie\ShortSchedule\Tests\Feature;

use Spatie\ShortSchedule\ShortSchedule;
use Spatie\ShortSchedule\Tests\TestCase;
use Spatie\ShortSchedule\Tests\TestClasses\TestKernel;

class ShortScheduleTest extends TestCase
{
    /** @test */
    public function it_can_schedule_commands_in_the_kernel()
    {
        $tempFilePath = $this->getTempFilePath("test.txt");

        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule->exec("touch '{$tempFilePath}'")->everySeconds(0.01)
        );

        $this->startAndStopShortScheduledAfterSeconds(0.02);

        $this->assertFileExists($tempFilePath);
    }

    protected function startAndStopShortScheduledAfterSeconds(float $seconds)
    {
        $loop = $this->getLoopThatStopsAfterSeconds($seconds);

        (new ShortSchedule($loop))->registerCommands()->start();
    }
}

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
        $loop = $this->getLoopThatStopsAfterSeconds(0.02);

        $tempFilePath = $this->getTempFilePath("test.txt");

        TestKernel::registerShortScheduleCommand(function (ShortSchedule $shortSchedule) use ($tempFilePath) {
            $shortSchedule->exec("touch '{$tempFilePath}'")->everySeconds(0.01);
        });

        (new ShortSchedule($loop))->registerCommands()->start();

        $this->assertFileExists($tempFilePath);
    }
}

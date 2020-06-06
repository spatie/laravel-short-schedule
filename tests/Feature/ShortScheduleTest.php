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
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$tempFilePath}'")
                ->everySeconds(0.1)
        );

        $this
            ->startAndStopShortScheduleAfterSeconds(0.29)
            ->assertFileContains($tempFilePath, 'called', 2);
    }

    protected function startAndStopShortScheduleAfterSeconds(float $seconds): self
    {
        $loop = $this->getLoopThatStopsAfterSeconds($seconds);

        (new ShortSchedule($loop))->registerCommands()->start();

        return $this;
    }
}

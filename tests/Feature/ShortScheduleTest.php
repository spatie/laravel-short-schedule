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
        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'")
                ->everySeconds(0.05)
        );

        $this
            ->startAndStopShortScheduleAfterSeconds(0.15)
            ->assertTempFileContains('called', 2);
    }

    /** @test */
    public function it_will_overlap_tasks_by_default()
    {
        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'; sleep 0.2")
                ->everySeconds(0.1)
        );

        $this
            ->startAndStopShortScheduleAfterSeconds(0.59)
            ->assertTempFileContains('called', 5);
    }

    /** @test */
    public function it_can_prevent_overlaps()
    {
        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'; sleep 0.2")
                ->everySeconds(0.1)
                ->withoutOverlapping()
        );

        $this
            ->startAndStopShortScheduleAfterSeconds(0.59)
            ->assertTempFileContains('called', 2);
    }

    /** @test */
    public function it_can_use_constraints()
    {
        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'")
                ->everySeconds(0.1)
                ->when(fn () => false)
        );

        $this
            ->startAndStopShortScheduleAfterSeconds(0.19)
            ->assertTempFileContains('called', 0);

        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'")
                ->everySeconds(0.1)
                ->when(fn () => true)
        );

        $this
            ->startAndStopShortScheduleAfterSeconds(0.19)
            ->assertTempFileContains('called', 1);
    }

    protected function startAndStopShortScheduleAfterSeconds(float $seconds): self
    {
        $loop = $this->getLoopThatStopsAfterSeconds($seconds);

        (new ShortSchedule($loop))->registerCommands()->start();

        return $this;
    }
}

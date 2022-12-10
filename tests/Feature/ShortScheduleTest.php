<?php

namespace Spatie\ShortSchedule\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Spatie\ShortSchedule\ShortSchedule;
use Spatie\ShortSchedule\Tests\TestCase;
use Spatie\ShortSchedule\Tests\TestClasses\TestKernel;

class ShortScheduleTest extends TestCase
{
    /** @test */
    public function it_will_execute_registered_command_in_the_shortSchedule_method_of_the_kernel()
    {
        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'")
                ->everySeconds(0.05)
        );

        $this
            ->runShortScheduleForSeconds(0.14)
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
            ->runShortScheduleForSeconds(0.59)
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
            ->runShortScheduleForSeconds(0.59)
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
            ->runShortScheduleForSeconds(0.19)
            ->assertTempFileContains('called', 0);

        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'")
                ->everySeconds(0.1)
                ->when(fn () => true)
        );

        $this
            ->runShortScheduleForSeconds(0.19)
            ->assertTempFileContains('called', 1);
    }

    /** @test **/
    public function it_wont_run_whilst_in_maintenance_mode()
    {
        $this->artisan('down')->assertExitCode(0);

        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'")
                ->everySeconds(0.05)
        );

        $this
            ->runShortScheduleForSeconds(0.14)
            ->assertTempFileContains('called', 0);

        $this->artisan('up')->assertExitCode(0);
    }

    /** @test **/
    public function it_will_run_whilst_in_maintenance_mode()
    {
        $this->artisan('down')->assertExitCode(0);

        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'")
                ->everySeconds(0.05)
                ->runInMaintenanceMode()
        );

        $this
            ->runShortScheduleForSeconds(0.14)
            ->assertTempFileContains('called', 2);

        $this->artisan('up')->assertExitCode(0);
    }

    /** @test **/
    public function do_not_run_if_already_running_on_another_server()
    {
        $key = 'framework'.DIRECTORY_SEPARATOR.'schedule-'.sha1('0.05'."echo 'called' >> '{$this->getTempFilePath()}'");
        Cache::add($key, true, 60);

        TestKernel::registerShortScheduleCommand(
            fn (ShortSchedule $shortSchedule) => $shortSchedule
                ->exec("echo 'called' >> '{$this->getTempFilePath()}'")
                ->everySeconds(0.05)
                ->onOneServer()
        );

        $this
            ->runShortScheduleForSeconds(0.14)
            ->assertTempFileContains('called', 0);
    }
}

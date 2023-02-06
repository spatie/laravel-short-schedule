<?php

use Illuminate\Support\Facades\Cache;
use Spatie\ShortSchedule\ShortSchedule;
use Spatie\ShortSchedule\Tests\TestClasses\TestKernel;

it('will execute registered command in the shortSchedule method of the kernel', function () {
    TestKernel::registerShortScheduleCommand(
        fn (ShortSchedule $shortSchedule) => $shortSchedule
            ->exec("echo 'called' >> '{$this->getTempFilePath()}'")
            ->everySeconds(0.05)
    );

    $this
        ->runShortScheduleForSeconds(0.14)
        ->assertTempFileContains('called', 2);
});

it('will overlap tasks by default', function () {
    TestKernel::registerShortScheduleCommand(
        fn (ShortSchedule $shortSchedule) => $shortSchedule
            ->exec("echo 'called' >> '{$this->getTempFilePath()}'; sleep 0.2")
            ->everySeconds(0.1)
    );

    $this
        ->runShortScheduleForSeconds(0.59)
        ->assertTempFileContains('called', 5);
});

it('can prevent overlaps', function () {
    TestKernel::registerShortScheduleCommand(
        fn (ShortSchedule $shortSchedule) => $shortSchedule
            ->exec("echo 'called' >> '{$this->getTempFilePath()}'; sleep 0.2")
            ->everySeconds(0.1)
            ->withoutOverlapping()
    );

    $this
        ->runShortScheduleForSeconds(0.59)
        ->assertTempFileContains('called', 2);
});

it('can use constraints', function () {
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
});

it("won't run whilst in maintenance mode", function () {
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
});

it('will run whilst in maintenance mode', function () {
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
});

test('do not run if already running on another server', function () {
    $key = 'framework' . DIRECTORY_SEPARATOR . 'schedule-' . sha1('0.05' . "echo 'called' >> '{$this->getTempFilePath()}'");
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
});

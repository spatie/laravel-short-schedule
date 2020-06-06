<?php

namespace Spatie\ShortSchedule\Tests;

use Illuminate\Contracts\Console\Kernel;
use Orchestra\Testbench\TestCase as Orchestra;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Spatie\ShortSchedule\ShortScheduleServiceProvider;
use Spatie\ShortSchedule\Tests\TestClasses\TestKernel;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class TestCase extends Orchestra
{
    private TemporaryDirectory $temporaryDirectory;

    public function setUp(): void
    {
        parent::setUp();

        TestKernel::clearShortScheduleCommands();

        $this->temporaryDirectory = (new TemporaryDirectory(__DIR__ . '/temp'));

        $this->temporaryDirectory->delete();

        $this->temporaryDirectory->force()->create();
    }

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

    public function getLoopThatStopsAfterSeconds(float $stopAfterSeconds): LoopInterface
    {
        $loop = Factory::create();

        $loop->addTimer($stopAfterSeconds, function () use ($loop) {
            $loop->stop();
        });

        return $loop;
    }

    public function getTempFilePath(string $fileName)
    {
        return $this->temporaryDirectory->path($fileName);
    }
}

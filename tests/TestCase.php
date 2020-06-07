<?php

namespace Spatie\ShortSchedule\Tests;

use Illuminate\Contracts\Console\Kernel;
use Orchestra\Testbench\TestCase as Orchestra;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use Spatie\ShortSchedule\ShortSchedule;
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

    public function getTempFilePath(string $fileName = 'test.txt'): string
    {
        return $this->temporaryDirectory->path($fileName);
    }

    protected function assertTempFileContains(string $needle, int $expectedCount): self
    {
        $haystack = file_exists($this->getTempFilePath())
            ? file_get_contents($this->getTempFilePath())
            : '';

        $actualCount = substr_count($haystack, $needle);

        $message = "Expected to find `{$needle}` {$expectedCount} time(s), but found it {$actualCount} time(s)";

        $this->assertEquals($expectedCount, $actualCount, $message);

        return $this;
    }

    protected function runShortScheduleForSeconds(float $seconds): self
    {
        $loop = $this->getLoopThatStopsAfterSeconds($seconds);

        (new ShortSchedule($loop))->registerCommands()->run();

        return $this;
    }
}

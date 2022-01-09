<?php declare(strict_types=1);

namespace Spatie\ShortSchedule\Tests\Unit;

use Illuminate\Console\Command;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionClass;
use Spatie\ShortSchedule\PendingShortScheduleCommand;

class PendingShortScheduleCommandTest extends Orchestra
{
    /** @test */
    public function it_will_generate_command_from_command_class(): void
    {
        $pendingCommand = new PendingShortScheduleCommand();
        $pendingCommand->command(TestCommand::class);
        $reflectionClass = new ReflectionClass($pendingCommand);

        $commandProperty = $reflectionClass->getProperty('command');
        $commandProperty->setAccessible(true);

        $artisanCommand = 'test-command';
        $this->assertEquals(PHP_BINARY . " artisan {$artisanCommand}", $commandProperty->getValue($pendingCommand));
    }
}

class TestCommand extends Command
{
    protected $signature = 'test-command';

    public function handle(): void
    {
    }
}

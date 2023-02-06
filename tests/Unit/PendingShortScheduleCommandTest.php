<?php

declare(strict_types=1);

namespace Spatie\ShortSchedule\Tests\Unit;

use Illuminate\Console\Command;
use Orchestra\Testbench\TestCase as Orchestra;
use ReflectionClass;
use Spatie\ShortSchedule\PendingShortScheduleCommand;

uses(Orchestra::class);

it('will generate command from command class', function () {
    $pendingCommand = new PendingShortScheduleCommand();
    $pendingCommand->command(TestCommand::class);
    $reflectionClass = new ReflectionClass($pendingCommand);

    $commandProperty = $reflectionClass->getProperty('command');
    $commandProperty->setAccessible(true);

    $artisanCommand = 'test-command';
    expect($commandProperty->getValue($pendingCommand))->toEqual(
        PHP_BINARY . " artisan {$artisanCommand}"
    );
});

class TestCommand extends Command
{
    protected $signature = 'test-command';

    public function handle(): void
    {
    }
}

<?php

namespace Spatie\ShortSchedule;

use App\Console\Kernel;
use React\EventLoop\Factory;
use ReflectionClass;
use Spatie\ShortSchedule\Events\ShortScheduledTaskStarting;
use Symfony\Component\Process\Process;

class ShortSchedule
{
    private array $commands = [];

    private array $processes = [];

    public function registerCommands(): self
    {
        $class = new ReflectionClass(Kernel::class);

        $shortScheduleMethod = $class->getMethod('shortSchedule');

        $shortScheduleMethod->setAccessible(true);

        $kernel = app(Kernel::class);

        $shortScheduleMethod->invokeArgs($kernel, [$this]);

        return $this;
    }

    public function command(string $command): PendingShortScheduleCommand
    {
        $pendingCommand = new PendingShortScheduleCommand($command);

        $this->commands[] = $pendingCommand;

        return $pendingCommand;
    }

    public function start(): void
    {
        $loop = Factory::create();

        collect($this->commands)->each(function (PendingShortScheduleCommand $command) use ($loop) {
            $loop->addPeriodicTimer($command->frequencyInSeconds, function () use ($command) {
                $commandString = $command->command;

                if (isset($this->processes[$commandString])) {
                    if ($this->processes[$commandString]->isRunning() && ! $command->allowOverlaps) {
                        return;
                    }
                }

                $process = Process::fromShellCommandline("php artisan {$commandString}");

                event(new ShortScheduledTaskStarting($command, $process));
                $process->start();
                event(new ShortScheduledTaskStarting($command, $process));

                $this->processes[$commandString] = $process;
            });
        });

        $loop->run();
    }
}

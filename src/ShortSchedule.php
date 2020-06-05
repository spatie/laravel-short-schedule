<?php

namespace Spatie\ShortSchedule;

use App\Console\Kernel;
use React\EventLoop\LoopInterface;
use ReflectionClass;
use Spatie\ShortSchedule\Events\ShortScheduledTaskStarting;
use Symfony\Component\Process\Process;

class ShortSchedule
{
    private LoopInterface $loop;

    private array $commands = [];

    private array $processes = [];

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
    }

    public function registerCommands(): self
    {
        $kernel = app(Kernel::class);

        $class = new ReflectionClass(get_class($kernel));

        $shortScheduleMethod = $class->getMethod('shortSchedule');

        $shortScheduleMethod->setAccessible(true);

        $shortScheduleMethod->invokeArgs($kernel, [$this]);

        return $this;
    }

    public function command(string $command): PendingShortScheduleCommand
    {
        $pendingCommand = (new PendingShortScheduleCommand())->command($command);

        $this->commands[] = $pendingCommand;

        return $pendingCommand;
    }

    public function exec(string $command): PendingShortScheduleCommand
    {
        $pendingCommand = (new PendingShortScheduleCommand())->exec($command);

        $this->commands[] = $pendingCommand;

        return $pendingCommand;
    }

    public function start(): void
    {
        collect($this->commands)->each(function (PendingShortScheduleCommand $shortScheduledCommand) {
            $this->registerCommand($shortScheduledCommand);
        });

        $this->loop->run();
    }

    protected function registerCommand(PendingShortScheduleCommand $shortScheduledCommand): void
    {
        $this->loop->addPeriodicTimer($shortScheduledCommand->frequencyInSeconds, function () use ($shortScheduledCommand) {
            $commandString = $shortScheduledCommand->command;

            if (isset($this->processes[$commandString])) {
                if ($this->processes[$commandString]->isRunning() && ! $shortScheduledCommand->allowOverlaps) {
                    return;
                }
            }

            if (! $shortScheduledCommand->shouldRun()) {
                return;
            }

            $process = Process::fromShellCommandline($commandString);

            event(new ShortScheduledTaskStarting($commandString, $process));
            $process->start();
            event(new ShortScheduledTaskStarting($commandString, $process));

            $this->processes[$commandString] = $process;
        });
    }

    protected function shouldRun()
    {
    }
}

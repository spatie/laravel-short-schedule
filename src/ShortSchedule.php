<?php

namespace Spatie\ShortSchedule;

use App\Console\Kernel;
use React\EventLoop\LoopInterface;
use ReflectionClass;

class ShortSchedule
{
    protected LoopInterface $loop;

    protected array $pendingCommands = [];

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

        $this->pendingCommands[] = $pendingCommand;

        return $pendingCommand;
    }

    public function exec(string $command): PendingShortScheduleCommand
    {
        $pendingCommand = (new PendingShortScheduleCommand())->exec($command);

        $this->pendingCommands[] = $pendingCommand;

        return $pendingCommand;
    }

    public function start(): void
    {
        collect($this->pendingCommands)
            ->map(function (PendingShortScheduleCommand $pendingCommand) {
                return new ShortScheduleCommand($pendingCommand);
            })
            ->each(function (ShortScheduleCommand $command) {
                $this->registerCommand($command);
            });

        $this->loop->run();
    }

    protected function registerCommand(ShortScheduleCommand $command): void
    {
        $this->loop->addPeriodicTimer($command->frequencyInSeconds(),  function () use ($command) {
            if (! $command->shouldRun()) {
                return;
            }

            $command->run();
        });
    }
}

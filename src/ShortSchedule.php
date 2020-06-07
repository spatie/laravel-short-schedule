<?php

namespace Spatie\ShortSchedule;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Traits\Macroable;
use React\EventLoop\LoopInterface;
use ReflectionClass;

class ShortSchedule
{
    use Macroable;

    protected LoopInterface $loop;

    protected array $pendingCommands = [];

    public function __construct(LoopInterface $loop)
    {
        $this->loop = $loop;
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

    public function registerCommands(): self
    {
        $kernel = app(Kernel::class);

        $class = new ReflectionClass(get_class($kernel));

        $shortScheduleMethod = $class->getMethod('shortSchedule');

        $shortScheduleMethod->setAccessible(true);

        $shortScheduleMethod->invokeArgs($kernel, [$this]);

        return $this;
    }

    public function run(): void
    {
        collect($this->pendingCommands)
            ->map(function (PendingShortScheduleCommand $pendingCommand) {
                return new ShortScheduleCommand($pendingCommand);
            })
            ->each(function (ShortScheduleCommand $command) {
                $this->addCommandToLoop($command, $this->loop);
            });

        $this->loop->run();
    }

    protected function addCommandToLoop(ShortScheduleCommand $command, LoopInterface $loop): void
    {
        $loop->addPeriodicTimer($command->frequencyInSeconds(),  function () use ($command) {
            if (! $command->shouldRun()) {
                return;
            }

            $command->run();
        });
    }
}

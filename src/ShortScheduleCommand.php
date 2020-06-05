<?php

namespace Spatie\ShortSchedule;

use Spatie\ShortSchedule\Events\ShortScheduledTaskStarting;
use Symfony\Component\Process\Process;

class ShortScheduleCommand extends PendingShortScheduleCommand
{
    private PendingShortScheduleCommand $pendingShortScheduleCommand;

    private ?Process $process = null;

    public function __construct(PendingShortScheduleCommand $pendingShortScheduleCommand)
    {
        $this->pendingShortScheduleCommand = $pendingShortScheduleCommand;
    }

    public function frequencyInSeconds(): int
    {
        return $this->pendingShortScheduleCommand->frequencyInSeconds;
    }

    public function shouldRun(): bool
    {
        if ($this->isRunning() && $this->pendingShortScheduleCommand->allowsOverlaps) {
            return false;
        }

        if (! $this->pendingShortScheduleCommand->shouldRun()) {
            return false;
        }

        return true;
    }

    public function isRunning(): bool
    {
        if (! $this->process) {
            return false;
        }

        return $this->process->isRunning();
    }

    public function run()
    {
        $commandString = $this->pendingShortScheduleCommand->command;

        $this->process = Process::fromShellCommandline($this->pendingShortScheduleCommand->command);

        event(new ShortScheduledTaskStarting($commandString, $this->process));
        $this->process->start();
        event(new ShortScheduledTaskStarting($commandString, $this->process));
    }
}

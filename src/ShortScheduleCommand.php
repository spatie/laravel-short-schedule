<?php

namespace Spatie\ShortSchedule;

use Illuminate\Support\Facades\App;
use Spatie\ShortSchedule\Events\ShortScheduledTaskStarted;
use Spatie\ShortSchedule\Events\ShortScheduledTaskStarting;
use Symfony\Component\Process\Process;

class ShortScheduleCommand extends PendingShortScheduleCommand
{
    protected PendingShortScheduleCommand $pendingShortScheduleCommand;

    protected ?Process $process = null;

    public function __construct(PendingShortScheduleCommand $pendingShortScheduleCommand)
    {
        $this->pendingShortScheduleCommand = $pendingShortScheduleCommand;
    }

    public function frequencyInSeconds(): float
    {
        return $this->pendingShortScheduleCommand->frequencyInSeconds;
    }

    public function shouldRun(): bool
    {
        if (App::isDownForMaintenance() && (! $this->pendingShortScheduleCommand->evenInMaintenanceMode)) {
            return false;
        }

        if ($this->isRunning() && (! $this->pendingShortScheduleCommand->allowOverlaps)) {
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

    public function run(): void
    {
        $commandString = $this->pendingShortScheduleCommand->command;
        $this->process = Process::fromShellCommandline($this->pendingShortScheduleCommand->command);

        event(new ShortScheduledTaskStarting($commandString, $this->process));
        $this->process->start();
        event(new ShortScheduledTaskStarted($commandString, $this->process));
    }
}

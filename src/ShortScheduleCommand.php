<?php

namespace Spatie\ShortSchedule;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
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
        $this->pendingShortScheduleCommand->getOnOneServer() ? $this->processOnOneServer() : $this->processCommand() ;
    }

    protected function processOnOneServer(): void
    {
        if (Cache::has($this->pendingShortScheduleCommand->cacheName())) {
            return;
        }

        Cache::add($this->pendingShortScheduleCommand->cacheName(), true, 60);

        $this->processCommand();
        $this->waitForProcessToFinish();

        Cache::forget($this->pendingShortScheduleCommand->cacheName());
    }

    private function processCommand(): void
    {
        $commandString = $this->pendingShortScheduleCommand->command;
        $this->process = Process::fromShellCommandline($commandString, base_path());

        event(new ShortScheduledTaskStarting($commandString, $this->process));
        $this->process->start();
        event(new ShortScheduledTaskStarted($commandString, $this->process));
    }

    private function waitForProcessToFinish(): void
    {
        while ($this->process->isRunning()) {
        }
    }
}

<?php

namespace Spatie\ShortSchedule;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Spatie\ShortSchedule\Events\ShortScheduledTaskStarted;
use Spatie\ShortSchedule\Events\ShortScheduledTaskStarting;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ShortScheduleCommand extends PendingShortScheduleCommand
{
    protected PendingShortScheduleCommand $pendingShortScheduleCommand;

    protected ?Process $process = null;

    protected int $count = 0;

    public function __construct(PendingShortScheduleCommand $pendingShortScheduleCommand)
    {
        $this->pendingShortScheduleCommand = $pendingShortScheduleCommand;
        $this->console = new ConsoleOutput($pendingShortScheduleCommand->verbosity);
    }

    public function frequencyInSeconds(): float
    {
        return $this->pendingShortScheduleCommand->frequencyInSeconds;
    }

    public function shouldRun(): bool
    {
        if (App::isDownForMaintenance() && (! $this->pendingShortScheduleCommand->evenInMaintenanceMode)) {
            $this->write("Skipping command (system is down): {$this->commandString()}", 'comment');

            return false;
        }

        if ($this->isRunning() && (! $this->pendingShortScheduleCommand->allowOverlaps)) {
            $this->write("Skipping command (still is running): {$this->commandString()}", 'comment');

            return false;
        }

        if (! $this->pendingShortScheduleCommand->shouldRun()) {
            return false;
        }

        if ($this->shouldRunOnOneServer()) {
            $this->write("Skipping command (has already run on another server): {$this->commandString()}", 'comment');

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

    protected function commandString(): string
    {
        return $this->pendingShortScheduleCommand->command;
    }

    protected function shouldRunOnOneServer(): bool
    {
        return $this->pendingShortScheduleCommand->getOnOneServer()
               && Cache::has($this->pendingShortScheduleCommand->cacheName());
    }

    protected function processOnOneServer(): void
    {
        Cache::add($this->pendingShortScheduleCommand->cacheName(), true, 60);

        $this->processCommand();
        $this->waitForProcessToFinish();

        Cache::forget($this->pendingShortScheduleCommand->cacheName());
    }

    private function processCommand(): void
    {
        $commandString = $this->commandString();
        $this->process = Process::fromShellCommandline($commandString, base_path());

        $this->write("Running command: {$commandString}");

        event(new ShortScheduledTaskStarting($commandString, $this->process));
        $this->process->start();
        event(new ShortScheduledTaskStarted($commandString, $this->process));
    }

    private function waitForProcessToFinish(): void
    {
        while ($this->process->isRunning()) {
        }
    }

    private function getExection(): string
    {
        return PHP_EOL.'Execution #'.(++$this->count).' in '.now()->isoFormat('L LTS').' output:';
    }

    private function write($string, $style = null): void
    {
        if (App::environment('testing') && $this->pendingShortScheduleCommand->verbosity === OutputInterface::VERBOSITY_NORMAL) {
            echo $this->getExection().$string;

            return;
        }

        $this->console->writeln('<info>'.$this->getExection().'</info>');

        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->console->writeln($styled);
    }
}

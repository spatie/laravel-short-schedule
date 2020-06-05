<?php

namespace Spatie\ShortSchedule;

class PendingShortScheduleCommand
{
    public string $command;

    public int $frequencyInSeconds = 1;

    public bool $allowOverlaps = true;

    public function __construct(string $command)
    {
        $this->command = $command;
    }

    public function everySecond(int $frequencyInSeconds = 1): self
    {
        return $this->everySeconds($frequencyInSeconds);
    }

    public function everySeconds(int $frequencyInSeconds = 1): self
    {
        $this->frequencyInSeconds = $frequencyInSeconds;

        return $this;
    }

    public function withoutOverlapping(): self
    {
        $this->allowOverlaps = false;

        return $this;
    }
}

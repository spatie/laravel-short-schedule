<?php

namespace Spatie\ShortSchedule\RunConstraints;

use Carbon\Carbon;

class BetweenConstraint implements RunConstraint
{
    protected string $startTime;

    protected string $endTime;

    public function __construct(string $startTime, string $endTime)
    {
        $this->startTime = $startTime;

        $this->endTime = $endTime;
    }

    public function shouldRun(): bool
    {
        $startTime = Carbon::createFromFormat('H:i', $this->startTime)->startOfMinute();

        $endTime = Carbon::createFromFormat('H:i', $this->endTime)->startOfMinute();

        if ($endTime->isBefore($startTime) && now()->setTimeFromTimeString($this->startTime)->isFuture()) {
            $startTime->subDay();
        }

        return now()->between($startTime, $endTime);
    }
}

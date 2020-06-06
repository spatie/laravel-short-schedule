<?php

namespace Spatie\ShortSchedule;

use Closure;
use Illuminate\Support\Arr;
use Spatie\ShortSchedule\RunConstraints\BetweenConstraint;
use Spatie\ShortSchedule\RunConstraints\EnvironmentConstraint;
use Spatie\ShortSchedule\RunConstraints\RunConstraint;
use Spatie\ShortSchedule\RunConstraints\WhenConstraint;

class PendingShortScheduleCommand
{
    protected string $command = '';

    protected int $frequencyInSeconds = 1;

    protected bool $allowsOverlaps = true;

    protected array $constraints = [];

    public function everySecond(int $frequencyInSeconds = 1): self
    {
        return $this->everySeconds($frequencyInSeconds);
    }

    public function everySeconds(int $frequencyInSeconds = 1): self
    {
        $this->frequencyInSeconds = $frequencyInSeconds;

        return $this;
    }

    public function command(string $artisanCommand):self
    {
        $this->command = "php artisan {$artisanCommand}";

        return $this;
    }

    public function exec(string $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function withoutOverlapping(): self
    {
        $this->allowsOverlaps = false;

        return $this;
    }

    public function shouldRun(): bool
    {
        $shouldNotRun = collect($this->constraints)
            ->contains(
                fn (RunConstraint $runConstraint) => ! $runConstraint->shouldRun()
            );

        return ! $shouldNotRun;
    }

    public function between(string $startTime, string $endTime): self
    {
        $this->constraints[] = new BetweenConstraint($startTime, $endTime);

        return $this;
    }

    /**
     * @param string|array $environments
     *
     * @return static
     */
    public function environments($environments): self
    {
        $environments = Arr::wrap($environments);

        $this->constraints[] = new EnvironmentConstraint($environments);

        return $this;
    }

    public function when(Closure $closure): self
    {
        $this->constraints[] = new WhenConstraint($closure);

        return $this;
    }
}

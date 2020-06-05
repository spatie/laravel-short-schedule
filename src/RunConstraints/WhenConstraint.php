<?php

namespace Spatie\ShortSchedule\RunConstraints;

use Closure;

class WhenConstraint implements RunConstraint
{
    protected Closure $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function shouldRun(): bool
    {
        $closure = $this->closure;

        return (bool)$closure();
    }
}

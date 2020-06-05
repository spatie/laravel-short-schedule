<?php

namespace Spatie\ShortSchedule\RunConstraints;

class WhenConstraint implements RunConstraint
{
    /** @var callable */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }
    
    public function shouldRun(): bool
    {
        $callable = $this->callable;

        return (bool)$callable();
    }
}

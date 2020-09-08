<?php

namespace Spatie\ShortSchedule\RunConstraints;

class EnvironmentConstraint implements RunConstraint
{
    protected array $allowedEnvironments;

    public function __construct(array $allowedEnvironments)
    {
        $this->allowedEnvironments = $allowedEnvironments;
    }

    public function shouldRun(): bool
    {
        return (bool) app()->environment($this->allowedEnvironments);
    }
}

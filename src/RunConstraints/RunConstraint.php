<?php

namespace Spatie\ShortSchedule\RunConstraints;

interface RunConstraint
{
    public function shouldRun(): bool;
}

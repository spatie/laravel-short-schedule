<?php

namespace Spatie\ShortSchedule\Tests\Unit\RunConstraints;

use Spatie\ShortSchedule\RunConstraints\EnvironmentConstraint;
use Spatie\ShortSchedule\Tests\TestCase;

class EnvironmentConstraintTest extends TestCase
{
    /** @test */
    public function it_will_constraint_execution_based_on_the_environment()
    {
        $constraint = new EnvironmentConstraint(['local', 'production']);
        $this->assertFalse($constraint->shouldRun());

        $constraint = new EnvironmentConstraint(['local', 'production', 'testing']);
        $this->assertTrue($constraint->shouldRun());
    }
}

<?php

namespace Spatie\ShortSchedule\Tests\Unit\RunConstraints;

use Spatie\ShortSchedule\Tests\TestCase;
use Spatie\ShortSchedule\RunConstraints\WhenConstraint;

class WhenConstraintTest extends TestCase
{
    /** @test */
    public function it_will_run_when_the_closure_allows_it()
    {
        $constraint = new WhenConstraint(fn () => true);
        $this->assertTrue($constraint->shouldRun());

        $constraint = new WhenConstraint(fn () => false);
        $this->assertFalse($constraint->shouldRun());
    }
}

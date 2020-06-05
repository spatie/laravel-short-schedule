<?php

namespace Spatie\ShortSchedule\Tests\Unit\RunConstraints;

use Spatie\ShortSchedule\RunConstraints\BetweenConstraint;
use Spatie\ShortSchedule\Tests\TestCase;
use Spatie\TestTime\TestTime;

class BetweenConstraintTest extends TestCase
{
    /** @test */
    public function it_will_constraint_execution_based_on_time()
    {
        TestTime::freeze('Y-m-d H:i:s', '2020-01-01 08:59:59');

        $constraint = new BetweenConstraint('09:00', '17:00');

        $this->assertFalse($constraint->shouldRun());

        TestTime::addSecond();
        $this->assertTrue($constraint->shouldRun());

        TestTime::freeze('Y-m-d H:i:s', '2020-01-01 17:00:00');
        $this->assertTrue($constraint->shouldRun());

        TestTime::addSecond();
        $this->assertFalse($constraint->shouldRun());
    }

    /** @test */
    public function the_constraint_will_work_for_overflowing_hours()
    {
        TestTime::freeze('Y-m-d H:i:s', '2020-01-01 20:59:59');

        $constraint = new BetweenConstraint('21:00', '01:00');

        $this->assertFalse($constraint->shouldRun());

        TestTime::addSecond();
        $this->assertTrue($constraint->shouldRun());

        TestTime::freeze('Y-m-d H:i:s', '2020-01-02 01:00:00');
        $this->assertTrue($constraint->shouldRun());

        TestTime::addSecond();
        $this->assertFalse($constraint->shouldRun());
    }
}

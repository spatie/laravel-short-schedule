<?php

namespace Spatie\ShortSchedule\Tests\Feature;

use Spatie\ShortSchedule\ShortSchedule;
use Spatie\ShortSchedule\Tests\TestCase;

class FeatureTest extends TestCase
{
    /** @test */
    public function this_is_a_feature()
    {


        (new ShortSchedule($loop))->registerCommands()->start();
    }
}

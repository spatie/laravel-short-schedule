<?php

namespace Spatie\ShortSchedule\Tests\Feature;

use React\EventLoop\Factory;
use Spatie\ShortSchedule\ShortSchedule;
use Spatie\ShortSchedule\Tests\TestCase;

class ShortScheduleTest extends TestCase
{
    /** @test */
    public function it_can_schedule_commands_in_the_kernel()
    {
        $loop = Factory::create();

        $loop->addTimer(2, function () use ($loop) {
            $loop->stop();
        });

        (new ShortSchedule($loop))->registerCommands()->start();
    }
}

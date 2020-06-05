<?php

namespace Spatie\ShortSchedule\Tests;

use React\EventLoop\Factory;

class ExampleTest extends TestCase
{
    /** @test */
    public function true_is_true()
    {
        $loop = Factory::create();

        $loop->addPeriodicTimer(5, function () {
            dd('here');
        });

        $loop->futureTick(function () use ($loop) {
            //$loop->stop();
            dump('here');
        });

        $loop->run();
    }
}

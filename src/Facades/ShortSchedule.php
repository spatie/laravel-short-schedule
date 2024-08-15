<?php

namespace Spatie\ShortSchedule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Spatie\ShortSchedule\ShortSchedule useLoop(\React\EventLoop\LoopInterface $loop)
 * @method static \Spatie\ShortSchedule\PendingShortScheduleCommand command(string $command)
 * @method static \Spatie\ShortSchedule\PendingShortScheduleCommand exec(string $command)
 * @method static \Spatie\ShortSchedule\ShortSchedule registerCommandsFromConsoleKernel()
 * @method static void run(?int $lifetime = null)
 */
class ShortSchedule extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Spatie\ShortSchedule\ShortSchedule::class;
    }
}

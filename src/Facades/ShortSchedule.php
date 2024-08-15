<?php

namespace Spatie\ShortSchedule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static useLoop(\React\EventLoop\LoopInterface $loop): \Spatie\ShortSchedule\ShortSchedule
 * @method static command(string $command): \Spatie\ShortSchedule\PendingShortScheduleCommand
 * @method static exec(string $command): \Spatie\ShortSchedule\PendingShortScheduleCommand
 * @method static registerCommandsFromConsoleKernel(): \Spatie\ShortSchedule\ShortSchedule
 * @method static run(?int $lifetime = null): void
 */
class ShortSchedule extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Spatie\ShortSchedule\ShortSchedule::class;
    }
}

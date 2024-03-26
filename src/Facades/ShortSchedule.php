<?php

namespace Spatie\ShortSchedule\Facades;

use Illuminate\Support\Facades\Facade;

class ShortSchedule extends Facade
{
    /**
     * @mixin \Spatie\ShortSchedule\ShortSchedule
     */
    public static function getFacadeAccessor()
    {
        return \Spatie\ShortSchedule\ShortSchedule::class;
    }
}

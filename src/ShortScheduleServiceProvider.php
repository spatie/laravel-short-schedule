<?php

namespace Spatie\ShortSchedule;

use Illuminate\Support\ServiceProvider;
use Spatie\ShortSchedule\Commands\ShortScheduleRunCommand;

class ShortScheduleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ShortScheduleRunCommand::class,
            ]);
        }
    }
}

<?php

namespace Spatie\ShortSchedule\Commands;

use Illuminate\Console\Command;
use Spatie\ShortSchedule\ShortSchedule;

class ShortScheduleRunCommand extends Command
{
    protected $signature = 'short-schedule:run';

    protected $description = 'Run the short scheduled commands';

    public function handle()
    {
        (new ShortSchedule())->registerCommands()->start();
    }
}

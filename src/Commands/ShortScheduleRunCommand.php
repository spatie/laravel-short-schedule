<?php

namespace Spatie\ShortSchedule\Commands;

use App\ShortSchedule;
use Illuminate\Console\Command;

class ShortScheduleRunCommand extends Command
{
    protected $signature = 'short-schedule:run';

    protected $description = 'Run the short scheduled commands';

    public function handle()
    {
        (new ShortSchedule())->registerCommands()->start();
    }
}

<?php

namespace Spatie\ShortSchedule\Commands;

use Illuminate\Console\Command;
use React\EventLoop\Factory;
use Spatie\ShortSchedule\ShortSchedule;

class ShortScheduleRunCommand extends Command
{
    protected $signature = 'short-schedule:run {--lifetime=}';

    protected $description = 'Run the short scheduled commands
                              {--lifetime= : The life time of worker';

    public function handle()
    {
        $loop = Factory::create();

        (new ShortSchedule($loop))->registerCommands()->run($this->option('lifetime'));
    }
}

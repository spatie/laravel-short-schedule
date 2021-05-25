<?php

namespace Spatie\ShortSchedule\Commands;

use Illuminate\Console\Command;
use React\EventLoop\Factory;
use Spatie\ShortSchedule\ShortSchedule;

class ShortScheduleRunCommand extends Command
{
    protected $signature = 'short-schedule:run {--life-time=}';

    protected $description = 'Run the short scheduled commands
                              {--life-time= : The life time of worker';

    public function handle()
    {
        $loop = Factory::create();

        (new ShortSchedule($loop))->registerCommands()->run($this->option('life-time'));
    }
}

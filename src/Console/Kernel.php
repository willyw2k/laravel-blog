<?php

namespace Daikazu\LaravelBlog\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Nova\Trix\PruneStaleAttachments;

class Kernel extends \App\Console\Kernel
{

    protected function schedule(Schedule $schedule)
    {
        parent::schedule($schedule);

        // Nova Trix Cleanup
        $schedule->call(function () {
            (new PruneStaleAttachments)();
        })->daily();
    }

}

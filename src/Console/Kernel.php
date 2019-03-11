<?php

namespace Daikazu\LaravelBlog\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Nova\Trix\PruneStaleAttachments;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
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

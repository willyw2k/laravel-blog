<?php

namespace Daikazu\LaravelBlog\Console\Commands;


use Daikazu\LaravelBlog\WordpressImport;
use Illuminate\Console\Command;

class ImportCommand extends Command
{

    const DEFAULT_TIMEOUT = 900;
    protected $signature = 'blog:import {--wp : Import Wordpress Blog} {--I|images : Import With Images} {--timeout= : Set Script Timeout} {url}';

    protected $description = 'Import data from other blogs';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {

        $timeout = $this->option('timeout');
        $withimages = $this->option('images');
        $url = $this->argument('url');

        $timeout = ($timeout) ? $timeout : SELF::DEFAULT_TIMEOUT;

        if ($this->option('wp')) {

            new WordpressImport($url, $withimages, $timeout);
            $this->error('Wordpress Imported');
        } else {

            $this->error('No BLog option');
        }


    }


}

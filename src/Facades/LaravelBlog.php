<?php

namespace Daikazu\LaravelBlog\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelBlog extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-blog';
    }
}

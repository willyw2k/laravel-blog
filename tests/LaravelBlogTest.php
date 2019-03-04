<?php

namespace Daikazu\LaravelBlog\Tests;

use Orchestra\Testbench\TestCase;
use Daikazu\LaravelBlog\ServiceProvider;
use Daikazu\LaravelBlog\Facades\LaravelBlog;

class LaravelBlogTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'laravel-blog' => LaravelBlog::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}

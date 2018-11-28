<?php

namespace Daikazu\LaravelBlog\Tests;

use Daikazu\LaravelBlog\Facades\LaravelBlog;
use Daikazu\LaravelBlog\ServiceProvider;
use Orchestra\Testbench\TestCase;

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

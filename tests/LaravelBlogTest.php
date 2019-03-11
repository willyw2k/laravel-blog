<?php

namespace Daikazu\LaravelBlog\Tests;

use Daikazu\LaravelBlog\Facades\LaravelBlog;
use Daikazu\LaravelBlog\Post;
use Daikazu\LaravelBlog\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class LaravelBlogTest extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->loadLaravelMigrations(['--database' => 'testing']);
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate', ['--database' => 'testing']);

        $this->withFactories(__DIR__ . '/../database/factories');
    }

    protected function getEnvironmentSetUp($app)
    {

        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('view.paths', [__DIR__ . '/resources/views']);
    }

    protected function resolveApplicationConsoleKernel($app)
    {
        $app->singleton( 'Illuminate\Contracts\Console\Kernel' , \Daikazu\LaravelBlog\Console\Kernel::class);
    }



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


    /** @test */
    public function can_create_post()
    {
        $post = factory(Post::class)->create([
            'title' => 'My Test Title',
        ]);

        $this->assertEquals('My Test Title', $post->title);
    }


    /** @test */
    public function posts_with_a_publish_at_date_after_now_and_a_before_publish_until_date()
    {
        $publishedPostA = factory(Post::class)->state('published')->create();

        $publishedPostB = factory(Post::class)->state('published')->create([
            'publish_until' => \Carbon\Carbon::parse('+3 days'),
        ]);

        $unpublishedPostA = factory(Post::class)->state('unpublished')->create([
            'publish_at' => \Carbon\Carbon::parse('+3 days'),
        ]);

        $unpublishedPostB = factory(Post::class)->state('unpublished')->create([
            'publish_until' => \Carbon\Carbon::parse('-3 days'),
        ]);

        $unpublishedPostC = factory(Post::class)->state('unpublished')->create();


        $publishedPosts = Post::published()->get();


        $this->assertTrue($publishedPosts->contains($publishedPostA));
        $this->assertTrue($publishedPosts->contains($publishedPostB));


        $this->assertFalse($publishedPosts->contains($unpublishedPostA));
        $this->assertFalse($publishedPosts->contains($unpublishedPostB));
        $this->assertFalse($publishedPosts->contains($unpublishedPostC));
    }
}

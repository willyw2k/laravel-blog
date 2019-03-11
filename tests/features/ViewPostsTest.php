<?php

namespace Daikazu\LaravelBlog\Tests\Features;


use Carbon\Carbon;
use Daikazu\LaravelBlog\Post;
use Orchestra\Testbench\TestCase;

class ViewPostsTest extends TestCase
{


    /** @test */
    function can_view_posts()
    {
        $this->loadLaravelMigrations();
//        $this->artisan('migrate', ['--database' => 'testbench'])->run();
        $this->loadLaravelMigrations(['--database' => 'testbench']);

        $post = Post::create([
            'title' => 'My Post Title',
            'slug' => 'my-post-title',
            'excerpt' => 'sfgjh lkdfg jlskgh ',
            'body' => 'sfghlksfghlkfgh',
            'meta_description' => 'sdfghsfkgjhklfgh',
            'seo_title' => 'dlsfjgksdfglkdfg',
            'publish_at' => \Carbon\Carbon::parse('-3 days'),
        ]);

        $this->assertEquals('My Post Title', $post->title);


    }

//    /** @test */
//    function can_get_published_date(){
//    $post = Post::create([
//        'published_at' => Carbon::parse('2019-02-25 8:00pm'),
//    ]);
//
//    $date = $post->published_date;
//
//    $this->assertEquals('2019-02-25 8:00pm', $date);

//}






}

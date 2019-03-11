<?php


use Daikazu\LaravelBlog\Post;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Post::class, function (Faker $faker) {

    $title = $faker->title;

    return [

        'title' => $title,
        'slug' => Str::slug($title),
        'excerpt' => $faker->paragraph(1),
        'body' => $faker->randomHtml(),
        'meta_description' => $faker->paragraph,
        'seo_title' => $faker->title,
        'publish_at' => \Carbon\Carbon::parse('-3 days'),

    ];
});

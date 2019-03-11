<?php


use Daikazu\LaravelBlog\Post;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'title'            => $faker->title,
        'slug'             => Str::slug($faker->unique()->languageCode),
        'excerpt'          => $faker->paragraph(1),
        'body'             => $faker->randomHtml(),
        'meta_description' => $faker->paragraph,
        'seo_title'        => $faker->title,
        'featured'         => false,
    ];
});


$factory->state(Post::class, 'published', function (Faker $faker) {
    return [
        'publish_at' => \Carbon\Carbon::parse('-3 days'),
    ];
});

$factory->state(Post::class, 'unpublished', function (Faker $faker) {
    return [
        'publish_at'    => null,
        'publish_until' => null,
    ];
});

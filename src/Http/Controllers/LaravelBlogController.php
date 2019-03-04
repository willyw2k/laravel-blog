<?php

namespace Daikazu\LaravelBlog\Http\Controllers;

use Daikazu\LaravelBlog\Post;
use Daikazu\LaravelBlog\Category;
use App\Http\Controllers\Controller;

class LaravelBlogController extends Controller
{
    public function index()
    {
        return view('laravel-blog::index');
    }

    public function post($slug)
    {
        $category = Category::where('slug', $slug)->count();

        if ($category) {
            return view('laravel-blog::category')->with('slug', $slug);
        }

        $post = Post::where('slug', $slug)->firstOrFail();

        return view('laravel-blog::post')->with('post', $post);
    }
}

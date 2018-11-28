<?php

namespace Daikazu\LaravelBlog\Http\Controllers;

use App\Http\Controllers\Controller;
use Daikazu\LaravelBlog\Category;
use Daikazu\LaravelBlog\Post;

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

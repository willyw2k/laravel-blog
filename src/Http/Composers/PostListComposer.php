<?php

namespace Daikazu\LaravelBlog\Http\Composers;

use Daikazu\LaravelBlog\Post;
use Illuminate\View\View;

class PostListComposer
{
    public function __construct(Post $posts)
    {
        $this->posts = $posts->published()
            ->orderBy('publish_at', config('laravel-blog.sort_order', 'desc'))
            ->paginate(config('laravel-blog.pagination'));
    }

    public function compose(View $view)
    {
        $view->with('posts', $this->posts);
    }
}

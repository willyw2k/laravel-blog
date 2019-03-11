<?php

namespace Daikazu\LaravelBlog\Http\Composers;

use Carbon\Carbon;
use Illuminate\View\View;
use Daikazu\LaravelBlog\Post;

class PostListComposer
{
    public function __construct(Post $posts)
    {

        $this->posts = $posts->where('is_published', true)
            ->where('publish_at', '<=', Carbon::now())
            ->where(function ($query) {
                $query->where('publish_until', '>=', Carbon::now())
                    ->orWhere('publish_until', null);
            })
            ->orderBy('publish_at', config('laravel-blog.sort_order', 'desc'))
            ->paginate(config('laravel-blog.pagination'));
    }

    public function compose(View $view)
    {
        $view->with('posts', $this->posts);
    }
}

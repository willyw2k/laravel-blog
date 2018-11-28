<?php

namespace Daikazu\LaravelBlog\Http\Composers;

use Carbon\Carbon;
use Daikazu\LaravelBlog\Post;
use Illuminate\View\View;

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
            ->orderBy('publish_at', 'desc')
            ->paginate(config('laravel-blog.pagination'));
    }


    public function compose(View $view)
    {
        $view->with('posts', $this->posts);
    }


}

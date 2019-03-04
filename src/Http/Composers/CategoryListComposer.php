<?php

namespace Daikazu\LaravelBlog\Http\Composers;

use Carbon\Carbon;
use Illuminate\View\View;
use Daikazu\LaravelBlog\Post;
use Daikazu\LaravelBlog\Category;

class CategoryListComposer
{
    /**
     * @var Post
     */
    private $posts;

    public function compose(View $view)
    {
        $slug = $view->getData()['slug'];

        $this->posts = Post::whereHas('category', function ($query) use ($slug) {
            $query->where('slug', $slug);
        })
            ->where('is_published', true)
            ->where('publish_at', '<=', Carbon::now())
            ->where(function ($query) {
                $query->where('publish_until', '>=', Carbon::now())
                    ->orWhere('publish_until', null);
            })
            ->orderBy('publish_at', 'desc')
            ->paginate(config('laravel-blog.pagination'));

        $category = Category::where('slug', $slug)->first();

        $view->with(['posts' => $this->posts, 'category' => $category]);
    }
}

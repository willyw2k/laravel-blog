<?php

namespace Daikazu\LaravelBlog;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Post extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $guarded = ['id'];

    protected $dates = [
        'publish_at',
        'publish_until',
    ];

    public function registerMediaConversions(Media $media = null)
    {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200)
            ->sharpen(5);
        $this->addMediaConversion('responsive')
            ->keepOriginalImageFormat()
            ->withResponsiveImages();
    }

    public function registerMediaCollections()
    {
        $this->addMediaCollection('image')->singleFile();
    }

    public function save(array $option = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->user_id && Auth::user()) {
            $this->user_id = Auth::user()->id;
        }

        // Auto Excerpt
        if (empty($this->excerpt)) {
            $this->excerpt = get_blog_excerpt(
                $this->body,
                config('laravel-blog.excerpt_word_length'),
                config('laravel-blog.excerpt_ellipses')
            );
        }

        parent::save();
    }


    public function userId()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopePublished(Builder $query)
    {
        return $query->whereNotNull('publish_at')
            ->where('publish_at', '<=', Carbon::now())
            ->where(function ($query) {
                $query->where('publish_until', '>=', Carbon::now())
                    ->orWhere('publish_until', null);
            });
    }

    public function getPublishedDateAttribute()
    {
        return $this->publish_at->toFormattedDateString();
    }

    public function getPublishedDateTimeAttribute()
    {
        return $this->publish_at->toDayDateTimeString();
    }

    public function getAuthorAttribute()
    {
        return $this->user->name;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function wordCount()
    {
        return get_blog_word_count($this->body);
    }
}

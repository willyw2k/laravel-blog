<?php

namespace Daikazu\LaravelBlog;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Post extends Model implements HasMedia
{
    use HasMediaTrait;
//    use HasMediaTrait, Cachable;

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
//        $this->addMediaCollection('gallery');
    }


    public function save(array $option = [])
    {
        // If no author has been assigned, assign the current user's id as the author of the post
        if (!$this->user_id && Auth::user()) {
            $this->user_id = Auth::user()->id;
        }

        // Auto Excerpt
        if (empty($this->excerpt)) {
            $this->excerpt = $this->get_excerpt($this->body, config('laravel-blog.excerpt_word_length'),
                config('laravel-blog.excerpt_ellipses'));
        }

        parent::save();
    }


    public function userId()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopePublished(Builder $query)
    {
        return $query->where('is_published', true);
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


    private function get_excerpt($content, $length = 40, $more = '...')
    {
        $excerpt = strip_tags(trim($content));
        $words = str_word_count($excerpt, 2);
        if (count($words) > $length) {
            $words = array_slice($words, 0, $length, true);
            end($words);
            $position = key($words) + strlen(current($words));
            $excerpt = substr($excerpt, 0, $position) . $more;
        }
        return $excerpt;
    }



}

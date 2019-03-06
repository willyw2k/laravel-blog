<?php

namespace Daikazu\LaravelBlog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Category extends Model
{

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'slug',
    ];

    public function save(array $option = [])
    {
        // Auto slug
        $this->slug = Str::slug($this->name);

        parent::save();
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

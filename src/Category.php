<?php

namespace Daikazu\LaravelBlog;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Category extends Model
{
    use Cachable;

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

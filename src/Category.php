<?php

namespace Daikazu\LaravelBlog;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

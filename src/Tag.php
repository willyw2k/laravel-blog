<?php

namespace Daikazu\LaravelBlog;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

//    use Cachable;

    protected $guarded = ['id'];


    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

}

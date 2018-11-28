<?php

namespace Daikazu\LaravelBlog;


class User extends \App\User
{
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}

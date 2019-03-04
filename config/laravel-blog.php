<?php

return [

    'prefix'     => env('BLOG_URL', 'blog'),
    'pagination' => env('BLOG_PAGINATION', 12),

    'excerpt_word_length' => env('BLOG_EXCERPT_WORD_LENGTH', 30),
    'excerpt_ellipses' => env('BLOG_EXCERPT_ELLIPSES', '...'),

    'sort_order' => 'desc',

];

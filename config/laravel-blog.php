<?php

return [

    'prefix'     => env('BLOG_URL', 'blog'),
    'pagination' => env('BLOG_PAGINATION', 12),

    'excerpt_word_length' => env('BLOG_EXCERPT_WORD_LENGTH', 30),
    'excerpt_ellipses' => env('BLOG_EXCERPT_ELLIPSES', '...'),

    'sort_order' => 'desc',

    // Disk for storing file uploads
    'file_disk' => env('BLOG_FILE_DISK', 'public'),

    //
    'schedule_tasks_running' => env('BLOG_TASKS_ON', true)

];

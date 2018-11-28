<?php

Route::Get('', 'LaravelBlogController@index')->name('index');
Route::Get('{slug}', 'LaravelBlogController@post')->name('post');

# Laravel Blog

[![Build Status](https://travis-ci.org/daikazu/laravel-blog.svg?branch=master)](https://travis-ci.org/daikazu/laravel-blog)
<!--[![styleci](https://styleci.io/repos/CHANGEME/shield)](https://styleci.io/repos/CHANGEME)-->
<!--[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/daikazu/laravel-blog/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/daikazu/laravel-blog/?branch=master)-->
<!--[![SensioLabsInsight](https://insight.sensiolabs.com/projects/CHANGEME/mini.png)](https://insight.sensiolabs.com/projects/CHANGEME)-->
<!--[![Coverage Status](https://coveralls.io/repos/github/daikazu/laravel-blog/badge.svg?branch=master)](https://coveralls.io/github/daikazu/laravel-blog?branch=master)-->

<!--[![Packagist](https://img.shields.io/packagist/v/daikazu/laravel-blog.svg)](https://packagist.org/packages/daikazu/laravel-blog)-->
<!--[![Packagist](https://poser.pugx.org/daikazu/laravel-blog/d/total.svg)](https://packagist.org/packages/daikazu/laravel-blog)-->
<!--[![Packagist](https://img.shields.io/packagist/l/daikazu/laravel-blog.svg)](https://packagist.org/packages/daikazu/laravel-blog)-->

Package description: CHANGE ME

## Installation

Install via composer
```bash
composer require daikazu/laravel-blog
```

### Register Service Provider

**Note! This and next step are optional if you use laravel>=5.5 with package
auto discovery feature.**

Add service provider to `config/app.php` in `providers` section
```php
Daikazu\LaravelBlog\ServiceProvider::class,
```

### Register Facade

Register package facade in `config/app.php` in `aliases` section
```php
Daikazu\LaravelBlog\Facades\LaravelBlog::class,
```

### Publish Configuration File


This will setup all necessary composer packages, migations, and publish all required files. If Nova is installed follow the installer prompts

```bash
php artisan blog:install
```

## Usage


Views are located in `resources/views/vendor/laravel-blog/` folder. Basic inline styles are added for example. Style to your hearts content.



### Wordpress Import

Download XML Export file from your WordPress Blog `Tools > Export > All Content`.


```bash
php artisan blog:import --wp --images wordpress_export.2019-02-28.xml
```
the `--images` will import featured image to storage using `spatie/laravel-medialibrary`

**Note: This Does Not make any changes to the Post body. Any images and or links in content will have to be updated as needed.**



## Security

If you discover any security related issues, please email 
instead of using the issue tracker.

## Credits

- [](https://github.com/daikazu/laravel-blog)
- [All contributors](https://github.com/daikazu/laravel-blog/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).

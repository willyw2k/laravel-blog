# Laravel Blog

[![Build Status](https://travis-ci.org/Daikazu/laravel-blog.svg?branch=master)](https://travis-ci.org/Daikazu/laravel-blog)
[![styleci](https://styleci.io/repos/159544237/shield)](https://styleci.io/repos/159544237)
<!--[![Coverage Status](https://coveralls.io/repos/github/daikazu/laravel-blog/badge.svg?branch=master)](https://coveralls.io/github/daikazu/laravel-blog?branch=master)-->

[![Packagist](https://img.shields.io/packagist/v/daikazu/laravel-blog.svg)](https://packagist.org/packages/daikazu/laravel-blog)
<!--[![Packagist](https://poser.pugx.org/daikazu/laravel-blog/d/total.svg)](https://packagist.org/packages/daikazu/laravel-blog)-->
<!--[![Packagist](https://img.shields.io/packagist/l/daikazu/laravel-blog.svg)](https://packagist.org/packages/daikazu/laravel-blog)-->

Package description: Quickly implement a Blog in you your Laravel Application
<br>

### WORK IN PROGRESS

Basic blog functionality is working but there is still more left to do. 



## Installation

Require the package using composer
```bash
composer require daikazu/laravel-blog
```

### Register Service Provider

**Note! If you use laravel>=5.7 this package will auto discovery.**


### Publish Configuration File

This will setup all necessary composer packages, migations, and publish all required files. If Nova is installed follow the installer prompts

```bash
php artisan blog:install
```

## Usage

Views are located in `resources/views/vendor/laravel-blog/` folder. Basic inline styles are added for example. Style to your hearts content.


### Nova Features


#### Nova Trix Field
This uses the Trix Field in Nova to handle image uploads. There is a scheduled task to handle clean up on that.

add `BLOG_TASKS_ON=false` to your `.env` file to turn off



### Wordpress Import

Download XML Export file from your WordPress Blog `Tools > Export > All Content`.

```bash
php artisan blog:import --wp --images wordpress_export.2019-02-28.xml
```
the `--images` will import featured image to storage using `spatie/laravel-medialibrary`

**Note: This Does Not make any changes to the Post body. Any images and or links in content will have to be updated as needed.**


## TODO
- pages
- category parent/children relations
- tagging
- more tests



## Security

If you discover any security related issues, please email 
instead of using the issue tracker.



## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.



## Credits

- [Mike Wall](https://github.com/daikazu)
- [All contributors](https://github.com/daikazu/laravel-blog/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](./LICENSE).

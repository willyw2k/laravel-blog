<?php

namespace Daikazu\LaravelBlog;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Route;
use Daikazu\LaravelBlog\Console\Commands\ImportCommand;
use Daikazu\LaravelBlog\Console\Commands\InstallCommand;
use Daikazu\LaravelBlog\Http\Composers\PostListComposer;
use Daikazu\LaravelBlog\Http\Composers\CategoryListComposer;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    const CONFIG_PATH = __DIR__.'/../config/laravel-blog.php';

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-blog');

        $this->registerResources();
        $this->registerComposers();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            self::CONFIG_PATH,
            'laravel-blog'
        );

        $this->app->bind('laravel-blog', function () {
            return new LaravelBlog();
        });

        $this->registerRoutes();

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->registerCommands();
        }

        if (config('laravel-blog.schedule_tasks_running')) {

            // If Nova Service provider Class Exists then it's okay to run the task
            if (class_exists('App\Providers\NovaServiceProvider')) {
                $this->app->singleton('laravel-blog.console.kernel', function ($app) {
                    $dispatcher = $app->make(\Illuminate\Contracts\Events\Dispatcher::class);

                    return new \Daikazu\LaravelBlog\Console\Kernel($app, $dispatcher);
                });

                $this->app->make('laravel-blog.console.kernel');
            }
        }
    }

    private function registerPublishing()
    {
        $this->publishes([
            self::CONFIG_PATH => config_path('laravel-blog.php'),
        ], 'laravel-blog-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'laravel-blog-migrations');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-blog'),
        ], 'laravel-blog-views');
    }

    private function registerCommands()
    {
        $this->commands([
            InstallCommand::class,
            ImportCommand::class,
        ]);
    }

    protected function registerResources()
    {
        $this->registerRoutes();
    }

    private function registerRoutes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    private function routeConfiguration()
    {
        return [
            'namespace'  => 'Daikazu\LaravelBlog\Http\Controllers',
            'domain'     => null,
            'as'         => 'blog.',
            'prefix'     => config('laravel-blog.prefix', 'blog'),
            'middleware' => 'web',
        ];
    }

    private function registerComposers()
    {
        View::composer('laravel-blog::index', PostListComposer::class);
        View::composer('laravel-blog::category', CategoryListComposer::class);
    }
}

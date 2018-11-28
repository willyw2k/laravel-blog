<?php

namespace Daikazu\LaravelBlog\Console\Commands;

use Daikazu\LaravelBlog\NovaPreset;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:install {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all Laravel Blog Assets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->info('Publishing Config');

        $this->call('vendor:publish', [
            '--tag'   => 'laravel-blog-config',
            '--force' => $this->option('force'),
        ]);

        $this->info('Publishing Migrations');

        $this->call('vendor:publish', [
            '--tag'   => 'laravel-blog-migrations',
            '--force' => $this->option('force'),
        ]);

        $this->info('Publishing Views');

        $this->call('vendor:publish', [
            '--tag'   => 'laravel-blog-views',
            '--force' => $this->option('force'),
        ]);


        $this->info('Updating Composer Packages');
        // Update Composer Packages
        $this->updateComposerPackages();


        $this->info('Installing Spatie Media Library as needed');

        if (count(File::glob(database_path('migrations/*_create_media_table.php'))) === 0) {
            $this->call('vendor:publish', [
                '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
                '--tag'      => 'migrations',
                '--force'    => $this->option('force'),
            ]);
        }

        $this->call('vendor:publish', [
            '--provider' => 'Spatie\ViewComponents\ViewComponentsServiceProvider',
            '--tag'      => 'config',
            '--force'    => $this->option('force'),
        ]);


        $this->call('vendor:publish', [
            '--provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
            '--tag'      => 'config',
            '--force'    => $this->option('force'),
        ]);


        $this->info('Running Migrations');
        try {
            $this->call('migrate');
        } catch (\Exception $e) {
            $this->warn($e->getMessage());
        }


        if (file_exists(app_path('Providers/NovaServiceProvider.php'))) {

            $this->info('Laravel Nova has been detected');

            if ($this->confirm('would you like to publish the associated files?')) {

                $this->updateComposerPackages(false, true);

                // Copy Nova Resources

                file_put_contents(app_path('Nova/Category.php'),
                    file_get_contents(__DIR__ . '/../../../Nova/Category.php'));
                file_put_contents(app_path('Nova/Post.php'), file_get_contents(__DIR__ . '/../../../Nova/Post.php'));
                file_put_contents(app_path('Nova/Tag.php'), file_get_contents(__DIR__ . '/../../../Nova/Tag.php'));
                file_put_contents(app_path('Nova/User.php'), file_get_contents(__DIR__ . '/../../../Nova/User.php'));

                $this->info('Nova scaffolding installed successfully.');

            }


        }


        $this->call('view:clear');
        $this->call('storage:link');


    }


    private function updateComposerPackageArray(array $packages)
    {
        return array_merge([
            'genealabs/laravel-model-caching' => '^0.3',
            'spatie/laravel-medialibrary'     => '^7.0.0',
            'spatie/laravel-view-components'  => '^1.1',

        ], Arr::except($packages, [

        ]));
    }


    private function updateNovaComposerPackageArray(array $packages)
    {
        return array_merge([
            'benjaminhirsch/nova-slug-field'    => '^1.1',
            'ebess/advanced-nova-media-library' => '^1.2',

        ], Arr::except($packages, [

        ]));
    }


    private function updateComposerPackages($dev = false, $nova = false)
    {
        if (!file_exists(base_path('composer.json'))) {
            return;
        }

        $configurationKey = $dev ? 'require-dev' : 'require';

        $packages = json_decode(file_get_contents(base_path('composer.json')), true);

        $packages[$configurationKey] = $this->updateComposerPackageArray(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : []
        );

        if ($nova) {
            $packages[$configurationKey] = $this->updateNovaComposerPackageArray(
                array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : []
            );
        }


        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('composer.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );

        shell_exec('composer update');

    }


}

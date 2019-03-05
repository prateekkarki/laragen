<?php
namespace Prateekkarki\Laragen;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Intervention\Image\ImageServiceProvider;
use Intervention\Image\Facades\Image;
use Prateekkarki\Laragen\Commands\Generate;
use Artisan;

class LaragenServiceProvider extends ServiceProvider
{
    /**
     * Run on application loading
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laragen.php' => config_path('laragen.php')
        ], 'config');

        Artisan::call('vendor:publish', [
            '--provider' => 'Prateekkarki\Laragen\LaragenServiceProvider'
        ]);
    }
    /**
     * Run after all boot method completed
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laragen.php',
            'laragen'
        );

        // Register Intervention Provider and Facade
		$this->app->register(ImageServiceProvider::class);
		AliasLoader::getInstance()->alias('Image', Image::class);

        $this->app->bind('command.laragen:make', Generate::class);

        $this->commands([
            'command.laragen:make',
        ]);

    }
    /**
     * To register laragen as first level command. E.g. laragen:generate
     *
     * @return array
     */
    public function provides()
    {
        return ['laragen'];
    }
}

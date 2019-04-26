<?php
namespace Prateekkarki\Laragen;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Intervention\Image\ImageServiceProvider;
use Intervention\Image\Facades\Image;
use Prateekkarki\Laragen\Commands\Generate;
use Prateekkarki\Laragen\Commands\Seeder;
use Prateekkarki\Laragen\Commands\Migrate;
use Prateekkarki\Laragen\Commands\Execute;
use Prateekkarki\Laragen\Commands\Initialize;
use Artisan;

class LaragenServiceProvider extends ServiceProvider
{
    /**
     * Run on application loading
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/options.php' => config_path('laragen/options.php'),
            __DIR__.'/../config/modules.php' => config_path('laragen/modules.php')
        ], 'config');

        Artisan::call('vendor:publish', [
            '--provider' => 'Prateekkarki\Laragen\LaragenServiceProvider'
        ]);
        
        $file = app_path('Http/Helpers/laragen_helpers.php');
        if (file_exists($file)) {
            require_once($file);
        }

    }
    /**
     * Run after all boot method completed
     */
    public function register()
    {
        // Register Intervention Provider and Facade
        $this->app->register(ImageServiceProvider::class);
        AliasLoader::getInstance()->alias('Image', Image::class);

        $this->app->bind('command.laragen:make', Generate::class);
        $this->app->bind('command.laragen:seed', Seeder::class);
        $this->app->bind('command.laragen:migrate', Migrate:: class);
        $this->app->bind('command.laragen:exec', Execute::class);
        $this->app->bind('command.laragen:init', Initialize::class);
        
        $this->commands([
            'command.laragen:make',
            'command.laragen:seed',
            'command.laragen:migrate',
            'command.laragen:exec',
            'command.laragen:init',
            ]);

        $routeFile = app_path('Providers\LaragenRouteServiceProvider.php');
        if (file_exists($routeFile))
            $this->app->register("\App\Providers\LaragenRouteServiceProvider");
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

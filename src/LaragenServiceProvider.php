<?php
namespace Prateekkarki\Laragen;

use Illuminate\Support\ServiceProvider;

class LaraCrudServiceProvider extends ServiceProvider
{
    /**
     * Run on application loading
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laragen.php' => config_path('laragen.php')
        ], 'laragen-config');
    }
    /**
     * Run after all boot method completed
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laragen.php', 
            'laragen'
        );
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

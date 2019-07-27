<?php
namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class LaragenRouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        Route::middleware(['web', 'auth'])
            ->prefix('admin')
            ->name('backend.')
            ->group(base_path('routes/backend/web.php'));

        Route::middleware(['api'])
            ->prefix('admin/api')
            ->name('backend.api.')
            ->group(base_path('routes/backend/api.php'));

        Route::middleware('web')
                ->prefix('admin')
                ->name('backend.')
                ->group(base_path('routes/backend/auth.php'));

        Route::middleware('web')
            ->group(base_path('routes/frontend/web.php'));
    }
}

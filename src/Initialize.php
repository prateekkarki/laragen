<?php

namespace Prateekkarki\Laragen;

class Initialize
{
    public static function initSetup()
    {
        copy(__DIR__ . '/../src/resources/stubs/RouteServiceProvider.stub', app_path('Providers/LaragenRouteServiceProvider.php'));
        if (!is_dir('routes/backend')) {
            @mkdir(base_path('routes/backend'), 0777, true);
            copy(__DIR__ . '/../src/resources/stubs/Route.stub', base_path('routes/backend/web.php'));
            copy(__DIR__ . '/../src/resources/stubs/Route.stub', base_path('routes/backend/auth.php'));
        }
        if (!is_dir('routes/frontend')) {
            @mkdir(base_path('routes/frontend'), 0777, true);
            copy(__DIR__ . '/../src/resources/stubs/Route.stub', base_path('routes/frontend/web.php'));
        }
    }

}
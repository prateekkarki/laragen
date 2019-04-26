<?php

namespace Prateekkarki\Laragen;

class Initialize
{
    public static function initSetup()
    {
        
        copy(__DIR__ . '/../src/resources/stubs/RouteServiceProvider.stub', __DIR__ . '/../../../../app/Providers/LaragenRouteServiceProvider.php');
        if (!is_dir('routes/backend')) {
            @mkdir(__DIR__ . '/../../../../routes/backend', 0777, true);
            copy(__DIR__ . '/../src/resources/stubs/Route.stub', __DIR__ . '/../../../../routes/backend/web.php');
            copy(__DIR__ . '/../src/resources/stubs/Route.stub', __DIR__ . '/../../../../routes/backend/auth.php');
        }
        if (!is_dir('routes/frontend')) {
            @mkdir(__DIR__ . '/../../../../routes/frontend', 0777, true);
            copy(__DIR__ . '/../src/resources/stubs/Route.stub', __DIR__ . '/../../../../routes/frontend/web.php');
        }
    }

}
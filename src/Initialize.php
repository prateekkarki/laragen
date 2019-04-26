<?php

namespace Prateekkarki\Laragen;

use Composer\Script\Event;

class Initialize
{
    public static function initSetup(Event $event)
    {
        echo "init...........";
        $composer = $event->getComposer();
        var_dump($composer);
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

    public static function preInstall(Event $event)
    {
        echo "pre install funcsn";

    }

    public static function postInstall(Event $event)
    {
        $composer = $event->getComposer();
    }

    public static function postPackageInstall(Event $event)
    {
        $installedPackage = $event->getComposer()->getPackage();
    }

}
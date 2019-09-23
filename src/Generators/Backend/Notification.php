<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

use Illuminate\Support\Str;

class Notification extends BaseGenerator implements GeneratorInterface
{
    private static $destination = "laragen/app/Notifications";
    private static $namespace  = "Laragen\App\Notifications";
    private static $template = "backend/notifications/notification";

    public function generate()
    {
        $fullFilePaths = [];
        $eventsType = config('laragen.options.events');
        
        foreach ($eventsType as $eventType)
        {
            $controllerTemplate = $this->buildTemplate(self::$template, [
                '{{namespace}}'          => self::$namespace,
                '{{modelName}}'          => $this->module->getModelName(),
                '{{eventTypeUppercase}}'          => Str::ucfirst($eventType),
                '{{eventTypeLowercase}}'          => Str::lower($eventType)
            ]);
            
            $fullFilePath = $this->getPath(self::$destination."/").$this->module->getModelName().Str::ucfirst($eventType)."Notification".".php";
            $fullFilePaths[] = $fullFilePath;
            file_put_contents($fullFilePath, $controllerTemplate);
        }
        
        return $fullFilePaths;
    }

}

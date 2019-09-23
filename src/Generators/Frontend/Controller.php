<?php
namespace Prateekkarki\Laragen\Generators\Frontend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Controller extends BaseGenerator implements GeneratorInterface
{
    
    private static $destination = "laragen/app/Http/Controllers/Frontend";
    private static $namespace = "Laragen\App\Http\Controllers/Frontend";
    private static $template = "frontend/Controller";

    public function generate()
    {
        $controllerTemplate = $this->buildTemplate(self::$template, [
            '{{namespace}}'                  => self::$namespace,
            '{{modelName}}'                  => $this->module->getModelName(),
            '{{moduleName}}'                 => $this->module->getModuleName(),
            '{{modelNameSingularLowerCase}}' => strtolower($this->module->getModelName())
        ]);
        
        $fullFilePath = $this->getPath(self::$destination."/").$this->module->getModelName()."Controller".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
    }
}

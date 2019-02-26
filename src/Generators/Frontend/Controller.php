<?php
namespace Prateekkarki\Laragen\Generators\Frontend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Controller extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $controllerTemplate = $this->buildTemplate('Controller', [
            '{{modelName}}'                  => $this->module->getModelName(),
            '{{moduleName}}'                 => $this->module->getModuleName(),
            '{{modelNameSingularLowerCase}}' => strtolower($this->module->getModelName())
        ]);
        
        $fullFilePath = $this->getPath("app/Http/Controllers/").$this->module->getModelName()."Controller".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
    }
}

<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Controller extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $controllerTemplate = $this->buildTemplate('Backend/Controller', [
            '{{modelName}}'                  => $this->module->getModelName(),
            '{{moduleName}}'                 => $this->module->getModuleName(),
            '{{modelNameLowercase}}' => strtolower($this->module->getModelName())
        ]);
        
        $fullFilePath = $this->getPath("app/Http/Controllers/Backend/").$this->module->getModelName()."Controller".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
    }
}

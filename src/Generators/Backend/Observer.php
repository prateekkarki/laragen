<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

use Illuminate\Support\Str;

class Observer extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $controllerTemplate = $this->buildTemplate('backend/observers/observer', [
            '{{modelName}}'           => $this->module->getModelName(),
            '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
        ]);
        
        $fullFilePath = $this->getPath("app/Observers/").$this->module->getModelName()."Observer".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
    }

}

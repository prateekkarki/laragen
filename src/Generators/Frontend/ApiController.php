<?php

namespace Prateekkarki\Laragen\Generators\Frontend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class ApiController extends BaseGenerator implements GeneratorInterface
{

    protected $destination = "laragen/app/Http/Controllers/Api/Frontend";
    protected $namespace  = "Laragen\App\Http\Controllers\Api\Frontend";
    protected $template  = "frontend/ApiController";
    protected $fileSuffix  = "ApiController";

    protected $childDestination = "app/Http/Controllers/Api/Frontend";
    protected $childNamespace  = "App\Http\Controllers\Api\Frontend";

    public function generate()
    {
        $controllerTemplate = $this->buildTemplate($this->template, [
            '{{namespace}}'                  => $this->namespace,
            '{{modelName}}'                  => $this->module->getModelName(),
            '{{moduleName}}'                 => $this->module->getModuleName(),
            '{{modelNameSingularLowerCase}}' => strtolower($this->module->getModelName())
        ]);

        return  $this->generateFile($controllerTemplate);
    }
}

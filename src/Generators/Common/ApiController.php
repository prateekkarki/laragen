<?php

namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class ApiController extends BaseGenerator implements GeneratorInterface
{
    protected $destination = "laragen/app/Http/Controllers/Api";
    protected $namespace  = "Laragen\App\Http\Controllers\Api";
    protected $template  = "common/ApiController";
    protected $fileSuffix  = "ApiController";

    protected $childDestination = "app/Http/Controllers/Api";
    protected $childNamespace  = "App\Http\Controllers\Api";

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

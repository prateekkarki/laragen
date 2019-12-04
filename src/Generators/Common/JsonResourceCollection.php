<?php

namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class JsonResourceCollection extends BaseGenerator implements GeneratorInterface
{
    protected $destination = "laragen/app/Http/Resources";
    protected $namespace  = "Laragen\App\Http\Resources";
    protected $template  = "common/Resources/JsonResourceCollection";
    protected $fileSuffix  = "ResourceCollection";

    protected $childDestination = "app/Http/Resources";
    protected $childNamespace  = "App\Http\Resources";

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

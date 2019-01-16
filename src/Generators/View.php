<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;

class View extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $controllerTemplate = $this->buildTemplate('Views/index', [
            '{{modelName}}'                  => $this->module->getModelName(),
            '{{moduleName}}'                 => $this->module->getModuleName(),
            '{{modelNameSingularLowerCase}}' => strtolower($this->module->getModelName())
        ]);

        file_put_contents(base_path("app/Http/Controllers/" . $this->module->getModelName() . "Controller" . ".php"), $controllerTemplate);
    }
}

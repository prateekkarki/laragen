<?php

namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Resource extends BaseGenerator implements GeneratorInterface
{
    protected $destination = "laragen/app/Http/Resources";
    protected $namespace  = "Laragen\App\Http\Resources";
    protected $template  = "common/Resources/Resource";
    protected $fileSuffix  = "Resource";

    protected $childDestination = "app/Http/Resources";
    protected $childNamespace  = "App\Http\Resources";

    public function generate()
    {
        $controllerTemplate = $this->buildTemplate($this->template, [
            '{{namespace}}'                  => $this->namespace,
            '{{modelName}}'                  => $this->module->getModelName(),
            '{{moduleName}}'                 => $this->module->getModuleName(),
            '{{modelNameSingularLowerCase}}' => strtolower($this->module->getModelName()),
            '{{resourceArray}}'              => $this->getResourceArray(),
        ]);

        return  $this->generateFile($controllerTemplate);
    }

    protected function getResourceArray()
    {
        foreach ($this->module->getColumns() as $column) {
            $rArr[] = "'{$column->getColumn()}'" . " => " . $column->getResourceTransformer();
        }
        $delimiter = ",\n{$this->getTabs(3)}";
        return implode($delimiter, $rArr);
    }
}

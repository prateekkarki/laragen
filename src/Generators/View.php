<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;

class View extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        // To be generated dynamically
        $viewsToBeGenerated = ['index'];
        
        foreach ($viewsToBeGenerated as $view) {
            $viewTemplate = $this->buildTemplate('Views/' . $view, [
                '{{modelName}}'                  => $this->module->getModelName(),
                '{{moduleName}}'                 => $this->module->getModuleName(),
                '{{modelNameSingularLowerCase}}' => strtolower($this->module->getModelName())
            ]);

            file_put_contents(base_path("resources/views/" . $this->module->getModuleName() . "/{$view}.blade.php"), $viewTemplate);            
        }
    }
}

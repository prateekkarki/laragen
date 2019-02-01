<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;

class View extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        // To be generated dynamically
        $viewsToBeGenerated = ['index', '_list'];
        
        foreach ($viewsToBeGenerated as $view) {
            $viewTemplate = $this->buildTemplate('Views/' . $view, [
                '{{modelNameSingularLowercase}}' => $this->module->getModelNameSingularLowercase(),
                '{{modelNamePlural}}'            => $this->module->getModelNamePlural(),
                '{{moduleName}}'                 => $this->module->getModuleName()
            ]);

            file_put_contents(base_path("resources/views/" . $this->module->getModuleName() . "/{$view}.blade.php"), $viewTemplate);            
        }
    }
}

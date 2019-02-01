<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;

class View extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        // To be generated dynamically
        $viewsToBeGenerated = ['index', '_list', '_show', '_empty'];
        
        foreach ($viewsToBeGenerated as $view) {
            $viewTemplate = $this->buildTemplate('Views/' . $view, [
                '{{modelNameSingularLowercase}}' => $this->module->getModelNameSingularLowercase(),
                '{{modelNamePlural}}'            => $this->module->getModelNamePlural(),
                '{{moduleName}}'                 => $this->module->getModuleName()
            ]);

            file_put_contents($this->getPath("resources/views/" . $this->module->getModuleName()). "/{$view}.blade.php", $viewTemplate);            
        }
    }
}

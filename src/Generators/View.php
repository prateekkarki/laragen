<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;

class View extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $viewsToBeGenerated = ['index', 'show', '_list', '_show', '_empty']; // To be generated dynamically

        $generatedFiles = [];
        foreach ($viewsToBeGenerated as $view) {
            $viewTemplate = $this->buildTemplate('Views/' . $view, [
                '{{modelNameSingularLowercase}}' => $this->module->getModelNameSingularLowercase(),
                '{{modelNamePlural}}'            => $this->module->getModelNamePlural(),
                '{{moduleName}}'                 => $this->module->getModuleName()
            ]);

            $fullFilePath = $this->getPath("resources/views/" . $this->module->getModuleName()) . "/{$view}.blade.php";
            file_put_contents($fullFilePath, $viewTemplate);
            $generatedFiles[] =  $fullFilePath;         
        }
        return $generatedFiles;
    }
}

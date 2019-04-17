<?php
namespace Prateekkarki\Laragen\Generators\Frontend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class View extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $viewsToBeGenerated = ['index', 'show', '_list', '_show', '_empty']; // To be generated dynamically

        $generatedFiles = [];
        foreach ($viewsToBeGenerated as $view) {
            $viewTemplate = $this->buildTemplate('frontend/views/' . $view, [
                '{{modelNameSingularLowercase}}' => $this->module->getModelNameLowercase(),
                '{{modelNamePlural}}'            => $this->module->getModelNamePlural(),
                '{{moduleName}}'                 => $this->module->getModuleName()
            ]);

            $fullFilePath = $this->getPath("resources/views/" . $this->module->getModuleName()) . "/{$view}.blade.php";
            file_put_contents($fullFilePath, $viewTemplate);
            $generatedFiles[] =  $fullFilePath;
        }

        $layoutPath = $this->getPath("resources/views/laragen/layouts/") . "app.blade.php";
        if(!file_exists($layoutPath)){

            $viewTemplate = $this->buildTemplate('frontend/views/layouts/app', []);
            file_put_contents($layoutPath, $viewTemplate);
            $generatedFiles[] =  $layoutPath;
        }

        return $generatedFiles;
    }
}

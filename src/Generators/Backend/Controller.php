<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Controller extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $controllerTemplate = $this->buildTemplate('backend/Controller', [
            '{{modelName}}'          => $this->module->getModelName(),
            '{{moduleName}}'         => $this->module->getModuleName(),
            '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
            '{{foreignData}}'        => $this->getForeignData(),
            '{{usedModels}}'         => $this->getUsedModels()
        ]);
        
        $fullFilePath = $this->getPath("app/Http/Controllers/Backend/").$this->module->getModelName()."Controller".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
    }

    protected function getForeignData(){
        $foreignData = "";
        $parents = $this->module->getForeignData();
        foreach($parents as $parent){
            $foreignData .= "'".$parent['parentModule']."' => ".$parent['parentModel']."::all()";
            $foreignData .= ($parent==last($parents)) ? '' : ', ';
        }
        return $foreignData;
    }

    protected function getUsedModels() {
        $foreignModels = $this->module->getForeignColumns();
        $namespace = "App\\Models\\";
        $usedModels = "use ".$namespace.$this->module->getModelName().";";

        foreach ($foreignModels as $models) {
            foreach ($models as $column => $module) {
                $namespace = ($module == 'users' && class_exists('App\\User')) ? "App\\" : "App\\Models\\";
                $class = $namespace.$this->moduleToModelName($module);
                $usedModels .= PHP_EOL."use ".$class.";";
            }
        }
        return $usedModels;
    }
}

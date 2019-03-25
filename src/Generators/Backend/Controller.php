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
            '{{fileUploads}}'       => $this->getFileUploads(),
            '{{foreignData}}'        => $this->getForeignData(),
            '{{usedModels}}'         => $this->getUsedModels()
        ]);
        
        $fullFilePath = $this->getPath("app/Http/Controllers/Backend/").$this->module->getModelName()."Controller".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
    }

    protected function getFileUploads(){
        $fileUploads = "";
        $fileFields = $this->module->getFileColumns();
        if(empty($fileFields)) return "";
        if(count($fileFields)>1){
            $fileUploads .= 'foreach (["'.implode('", "', $fileFields).'"] as $fileField) {'.PHP_EOL;
            $fileUploads .= $this->getTabs(3).'if ($request->has($fileField)) {'.PHP_EOL;
            $fileUploads .= $this->getTabs(4).'$this->uploader->process($request->input($fileField), "'.$this->module->getModelNameLowercase().'");'.PHP_EOL;
            $fileUploads .= $this->getTabs(3).'}'.PHP_EOL;
            $fileUploads .= $this->getTabs(2).'}'.PHP_EOL;
        }else{
            $fileField = $fileFields[0];
            $fileUploads .= 'if ($request->has("'.$fileField.'")) {'.PHP_EOL;
            $fileUploads .= $this->getTabs(3).'$this->uploader->process($request->input("'.$fileField.'"), "category");'.PHP_EOL;
            $fileUploads .= $this->getTabs(2).'}'.PHP_EOL;
        }
        return $fileUploads;
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

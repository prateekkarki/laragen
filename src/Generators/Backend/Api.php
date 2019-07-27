<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Api extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $controllerTemplate = $this->buildTemplate('backend/Api', [
            '{{modelName}}'          => $this->module->getModelName(),
            '{{moduleName}}'         => $this->module->getModuleName(),
            '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
            '{{fileUploads}}'        => $this->getFileUploads(),
            '{{relatedUpdates}}'     => $this->getRelatedUpdates(),
            '{{createRelated}}'      => $this->getCreateRelated(),
            '{{foreignData}}'        => $this->getForeignData(),
            '{{usedModels}}'         => $this->getUsedModels(),
            '{{perPage}}'            => config("laragen.options.listing_per_page")
        ]);
        
        $fullFilePath = $this->getPath("app/Http/Controllers/Backend/Api/").$this->module->getModelName()."Controller".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
    }

    protected function getCreateRelated() {
        $relatedUpdates = "";
        $relatedTypes = $this->module->getFilteredColumns(['hasPivot']);
        
        if (empty($relatedTypes)) return "";
        if (count($relatedTypes) > 1) {
            $relatedUpdates .= $this->buildTemplate('backend/fragments/related-create', [
                '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
                '{{relatedTypes}}'         => implode('", "', $this->module->getFilteredColumns(['hasPivot', 'hasModel'], true)),
            ]);
        } else {
            $type = $relatedTypes[0];
            $relatedUpdates .= 'if ($request->has("'.$type->getColumn().'")) {'.PHP_EOL;
            $relatedUpdates .= $this->getTabs(3).'$'.$this->module->getModelNameLowercase().'->'.$type->getColumn().'()->attach($request->input("'.$type->getColumn().'"));'.PHP_EOL;
            $relatedUpdates .= $this->getTabs(2).'}'.PHP_EOL;
        }
        return $relatedUpdates;
    }

    protected function getRelatedUpdates() {
        $relatedUpdates = "";
        $relatedTypes = $this->module->getFilteredColumns(['hasPivot']);
        if (empty($relatedTypes)) return "";
        if (count($relatedTypes) > 1) {
            $relatedUpdates .= $this->buildTemplate('backend/fragments/related-process', [
                '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
                '{{relatedTypes}}'         => implode('", "', $this->module->getFilteredColumns(['hasPivot'], true)),
            ]);
        } else {
            $type = $relatedTypes[0];
            $relatedUpdates .= 'if ($request->has("'.$type->getColumn().'")) {'.PHP_EOL;
            $relatedUpdates .= $this->getTabs(3).'$'.$this->module->getModelNameLowercase().'->'.$type->getColumn().'()->sync($request->input("'.$type->getColumn().'"));'.PHP_EOL;
            $relatedUpdates .= $this->getTabs(2).'}'.PHP_EOL;
        }
        return $relatedUpdates;
    }

    protected function getFileUploads() {
        $fileUploads = "";
        $fileFields = $this->module->getFilteredColumns(['hasFile']);
        if (empty($fileFields)) return "";
        if (count($fileFields) > 1) {
            $fileUploads .= $this->buildTemplate('backend/fragments/upload-process', [
                '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
                '{{fileFields}}'         => implode('", "', $this->module->getFilteredColumns(['hasFile'], true)),
            ]);
        } else {
            $fileField = $fileFields[0];
            $fileUploads .= 'if ($request->has("'.$fileField->getColumn().'")) {'.PHP_EOL;
            $fileUploads .= $this->getTabs(3).'$this->uploader->process($request->input("'.$fileField->getColumn().'"), "category");'.PHP_EOL;
            $fileUploads .= $this->getTabs(2).'}'.PHP_EOL;
        }
        return $fileUploads;
    }

    protected function getForeignData() {
        $foreignData = "";
        $parents = $this->module->getFilteredColumns(['hasPivot','hasSingleRelation']);
        $columns = [];
        foreach ($parents as $type) {
            $column = $type->getRelatedModule();
            if(!in_array($column, $columns)){
                $foreignData .= "'".$column."' => ".$type->getRelatedModel()."::all(),".PHP_EOL.$this->getTabs(3);
                $columns[] = $column;
            }
        }
        return $foreignData;
    }

    protected function getUsedModels() {
        $namespace = "App\\Models\\";
        $usedModels = "use ".$namespace.$this->module->getModelName().";";

        $classes = [$namespace.$this->module->getModelName()];
        foreach($this->module->getFilteredColumns(['hasSingleRelation', 'hasPivot', 'hasModel']) as $type){
            $model = $type->getRelatedModel();
            $class = ($model == 'User') ? config('laragen.options.user_model') : "App\\Models\\".$model;
            if(in_array($class, $classes)){
                continue;
            }
            $classes[] = $class;
            $usedModels .= PHP_EOL."use ".$class.";";
        }
        return $usedModels;
    }
}

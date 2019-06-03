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
            '{{fileUploads}}'        => $this->getFileUploads(),
            '{{relatedUpdates}}'     => $this->getRelatedUpdates(),
            '{{createRelated}}'      => $this->getCreateRelated(),
            '{{foreignData}}'        => $this->getForeignData(),
            '{{usedModels}}'         => $this->getUsedModels(),
            '{{perPage}}'            => config("laragen.options.listing_per_page")
        ]);
        
        $fullFilePath = $this->getPath("app/Http/Controllers/Backend/").$this->module->getModelName()."Controller".".php";
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
            $relatedUpdates .= $this->getTabs(2).'if ($request->has("'.$type->getColumn().'")) {'.PHP_EOL;
            $relatedUpdates .= $this->getTabs(3).'$'.$this->module->getModelNameLowercase().'->'.$type->getColumn().'()->sync($request->input("'.$type->getColumn().'"));'.PHP_EOL;
            $relatedUpdates .= $this->getTabs(2).'}'.PHP_EOL;
        }
        return $relatedUpdates;
    }

    protected function getFileUploads() {
        $fileUploads = "";
        $fileFields = $this->module->getFilteredColumns(['hasImage', 'hasFile']);
        foreach ($fileFields as $fileField) {
            $processMethod = $fileField->hasFile() ? 'process' : 'processImage';
            $fileUploads .= $this->getTabs(2).'if ($request->has("'.$fileField->getColumn().'")) {'.PHP_EOL;
            if ($fileField->hasMultipleFiles()) {
                $fileUploads .= $this->getTabs(3).'$'.$fileField->getColumn().'=[];'.PHP_EOL;
                $fileUploads .= $this->getTabs(3).'foreach($request->input("'.$fileField->getColumn().'") as $input){'.PHP_EOL;
                $fileUploads .= $this->getTabs(4).'$uploadData = $this->uploader->'.$processMethod.'($input, "'.$this->module->getModelNameLowercase().'");'.PHP_EOL;
                $fileUploads .= $this->getTabs(4).'$'.$fileField->getColumn().'[] = new '.$fileField->getRelatedModel().'($uploadData);'.PHP_EOL;
                $fileUploads .= $this->getTabs(3).'}'.PHP_EOL;
                $fileUploads .= $this->getTabs(3).'$'.$this->module->getModelNameLowercase().'->'.$fileField->getColumn().'()->saveMany($'.$fileField->getColumn().');'.PHP_EOL;

            }else{
                $fileUploads .= $this->getTabs(3).'$uploadData = $this->uploader->'.$processMethod.'($request->input("'.$fileField->getColumn().'"), "'.$this->module->getModelNameLowercase().'");'.PHP_EOL;
                $fileUploads .= $this->getTabs(3).'if (empty($uploadData["errors"])) {'.PHP_EOL;
                $fileUploads .= $this->getTabs(4).'$updateData["'.$fileField->getColumn().'"] = $uploadData["filename"];'.PHP_EOL;
                $fileUploads .= $this->getTabs(3).'}'.PHP_EOL;
            }
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

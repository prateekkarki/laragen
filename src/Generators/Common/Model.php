<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Illuminate\Support\Str;

class Model extends BaseGenerator implements GeneratorInterface
{
    private static $destination = "laragen/app/Models";
    private static $namespace  = "Laragen\App\Models";
    private static $template = "common/Models/Model";
    private static $pivotTemplate = "common/Models/Pivot";

    public function generate()
    {
        $generatedFiles = [];
        $modelTemplate = $this->buildTemplate(self::$template, [
            '{{namespace}}'     => self::$namespace,
            '{{modelName}}'       => $this->module->getModelName(),
            '{{massAssignables}}' => implode("', '", $this->module->getColumns(true, true)),
            '{{usedModels}}'      => $this->getUsedModels(),
            '{{foreignMethods}}'  => $this->getForeignMethods()
        ]);
        
        $fullFilePath = $this->getPath(self::$destination."/").$this->module->getModelName().".php";
        file_put_contents($fullFilePath, $modelTemplate);
        $generatedFiles[] = $fullFilePath;
        
        foreach ($this->module->getFilteredColumns('hasPivot') as $type) {
            $typeTemplate = $this->buildTemplate(self::$pivotTemplate, [
                '{{namespace}}'     => self::$namespace,
                '{{pivotName}}'       => $type->getPivot(),
                '{{massAssignables}}' => implode("', '", $type->getTypeColumns()),
                '{{foreignMethods}}'  => $this->getTypeForeignMethods($type),
            ]);
            $fullFilePath = $this->getPath(self::$destination."/").$type->getPivot().".php";
            file_put_contents($fullFilePath, $typeTemplate);
            $generatedFiles[] = $fullFilePath;
        }
        
        foreach ($this->module->getFilteredColumns(['hasModel', 'hasOptions']) as $type) {
            $pivotModel = Str::singular($type->getPivot());
            $typeTemplate = $this->buildTemplate(self::$template, [
                '{{namespace}}'     => self::$namespace,
                '{{modelName}}'       => $pivotModel,
                '{{massAssignables}}' => implode("', '", $type->getTypeColumns()),
                '{{usedModels}}'      => $this->getUsedModels($pivotModel),
                '{{foreignMethods}}'  => $this->getTypeForeignMethods($type),
            ]);
            
            $fullFilePath = $this->getPath(self::$destination."/").$pivotModel.".php";
            file_put_contents($fullFilePath, $typeTemplate);
            $generatedFiles[] = $fullFilePath;
        }

        return $generatedFiles;
    }

    protected function getTypeForeignMethods($type)
    {
        $foreignMethods = "";
        $stub = $type->getStub('modelMethod') ?: 'common/Models/fragments/belongsTo';
        $foreignMethods .= $this->buildTemplate($stub, [
            '{{columnName}}'  => $type->getColumn(),
            '{{parent}}'      => $type->getParentModelLowercase(),
            '{{parentModel}}' => $type->getParentModel(),
            '{{relatedModel}}' => $type->getRelatedModel(),
        ]);

        return $foreignMethods;
    }

    protected function getUsedModels($pivotModel = false) {
        $usedModels = "";
        $classes = [];
        foreach ($this->module->getFilteredColumns(['hasSingleRelation', 'hasPivot', 'hasModel']) as $type) {
            $model = $type->getRelatedModel();
            $class = ($model == 'User') ? config('laragen.options.user_model') : "App\\Models\\".$model;
            if (in_array($class, $classes) || $model == $this->module->getModelName() || $model == $pivotModel) {
                continue;
            }
            $classes[] = $class;
            $usedModels .= PHP_EOL."use ".$class.";";
        }
        return $usedModels;
    }

    protected function getForeignMethods()
    {
        $foreignMethods = "";
        foreach ($this->module->getFilteredColumns(['hasPivot', 'hasSingleRelation', 'hasModel']) as $type) {
            $stub = $type->getStub('foreignMethod') ?: 'common/Models/fragments/hasOne';
            $foreignMethods .= $this->buildTemplate($stub, [
                '{{columnName}}'   => $type->getColumn(),
                '{{parent}}'       => $type->getParentModelLowercase(),
                '{{relatedModel}}' => $type->getRelatedModel(),
                '{{table}}'        => $type->getPivotTable(),
                '{{parentModel}}' => $type->getParentModel(),
                '{{parentId}}'     => $type->getParentModelLowercase()."_id",
                '{{childId}}'      => $type->getChildKey(),
            ]);
        }
        return $foreignMethods;
    }
}

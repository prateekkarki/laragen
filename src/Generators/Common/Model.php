<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Model extends BaseGenerator implements GeneratorInterface
{
    protected $destination = "laragen/app/Models";
    protected $namespace  = "Laragen\App\Models";
    protected $template = "common/Models/Model";
    protected $pivotTemplate = "common/Models/Pivot";

    protected $childDestination = "app/Models";
    protected $childNamespace  = "App\Models";

    public function generate()
    {
        $generatedFiles = [];
        $modelTemplate = $this->buildTemplate($this->template, [
            '{{namespace}}'     => $this->namespace,
            '{{modelName}}'       => $this->module->getModelName(),
            '{{massAssignables}}' => implode("', '", $this->module->getColumns(true, true)),
            '{{usedModels}}'      => $this->getUsedModels(),
            '{{foreignMethods}}'  => $this->getForeignMethods()
        ]);

        $generatedFiles[] = $this->generateFile($modelTemplate);

        foreach ($this->module->getFilteredColumns('hasPivot') as $type) {
            $typeTemplate = $this->buildTemplate($this->pivotTemplate, [
                '{{namespace}}'     => $this->namespace,
                '{{pivotName}}'       => $type->getPivot(),
                '{{massAssignables}}' => implode("', '", $type->getTypeColumns()),
                '{{foreignMethods}}'  => $this->getTypeForeignMethods($type),
            ]);

            $generatedFiles[] = $this->generateFile($typeTemplate, $type->getPivot());
        }

        foreach ($this->module->getFilteredColumns(['hasModel', 'hasOptions']) as $type) {
            $pivotModel = $type->getPivot();
            $typeTemplate = $this->buildTemplate($this->template, [
                '{{namespace}}'     => $this->namespace,
                '{{modelName}}'       => $pivotModel,
                '{{massAssignables}}' => implode("', '", $type->getTypeColumns()),
                '{{usedModels}}'      => $this->getUsedModels($pivotModel),
                '{{foreignMethods}}'  => $this->getTypeForeignMethods($type),
            ]);

            $generatedFiles[] = $this->generateFile($typeTemplate, $pivotModel);
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
                '{{parentModel}}'  => $type->getParentModel(),
                '{{parentId}}'     => $type->getParentModelLowercase()."_id",
                '{{childId}}'      => $type->getChildKey(),
            ]);
        }
        return $foreignMethods;
    }
}

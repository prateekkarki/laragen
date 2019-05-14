<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Illuminate\Support\Str;

class Model extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $generatedFiles = [];
        $modelTemplate = $this->buildTemplate('common/Models/Model', [
            '{{modelName}}'       => $this->module->getModelName(),
            '{{massAssignables}}' => implode("', '", $this->module->getColumns(true, true)),
            '{{foreignMethods}}'  => $this->getForeignMethods()
        ]);
        
        $fullFilePath = $this->getPath("app/Models/").$this->module->getModelName().".php";
        file_put_contents($fullFilePath, $modelTemplate);
        $generatedFiles[] = $fullFilePath;
        
        foreach($this->module->getFilteredColumns('hasPivot') as $type){
            $typeTemplate = $this->buildTemplate('common/Models/Pivot', [
                '{{pivotName}}'       => $type->getPivot(),
                '{{massAssignables}}' => implode("', '", $type->getTypeColumns()),
                '{{foreignMethods}}'  => $this->getTypeForeignMethods($type),
            ]);
            $fullFilePath = $this->getPath("app/Models/").$type->getPivot().".php";
            file_put_contents($fullFilePath, $typeTemplate);
            $generatedFiles[] = $fullFilePath;
        }
        
        foreach($this->module->getFilteredColumns(['hasModel', 'hasOptions']) as $type){
            $typeTemplate = $this->buildTemplate('common/Models/Model', [
                '{{modelName}}'       => Str::singular($type->getPivot()),
                '{{massAssignables}}' => implode("', '", $type->getTypeColumns()),
                '{{foreignMethods}}'  => $this->getTypeForeignMethods($type),
            ]);
            
            $fullFilePath = $this->getPath("app/Models/").Str::singular($type->getPivot()).".php";
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
        ]);

        return $foreignMethods;
    }

    protected function getForeignMethods()
    {
        $foreignMethods = "";
        foreach($this->module->getFilteredColumns(['hasPivot', 'hasSingleRelation', 'hasModel']) as $type){
            $stub = $type->getStub('foreignMethod') ?: 'common/Models/fragments/hasOne';
            $foreignMethods .= $this->buildTemplate($stub, [
                '{{columnName}}'   => $type->getColumn(),
                '{{parent}}'       => $type->getParentModelLowercase(),
                '{{relatedModel}}' => $type->getRelatedModel(),
                '{{table}}'        => $type->getPivotTable(),
                '{{parentId}}'     => $type->getParentModelLowercase() . "_id",
                '{{childId}}'      => $type->getChildKey(),
            ]);
        }
        return $foreignMethods;
    }
}

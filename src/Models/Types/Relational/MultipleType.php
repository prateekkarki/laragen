<?php
namespace Prateekkarki\Laragen\Models\Types\Relational;
use Prateekkarki\Laragen\Models\Types\RelationalType;
use Illuminate\Support\Str;

class MultipleType extends RelationalType
{
    protected $hasPivot = true;

    public function getPivotSchema($modelName, $moduleName)
    {
        $schema = PHP_EOL.$this->getTabs(3);
        $schema .= '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');".PHP_EOL.$this->getTabs(3);

        $schema .= '$table->bigInteger("'. Str::singular($this->columnName) .'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('". Str::singular($this->columnName) ."_id')->references('id')->on('".$this->columnName."')->onDelete('set null');".PHP_EOL;
        return $schema;
    }
    
    public function getPivotFile($model)
    {
        $modelArray = [strtolower($this->getModelName()), $model];
        sort($modelArray);
        return implode("_", $modelArray);
    }
    
    public function getPivotTableName($model)
    {
        $modelArray = [strtolower($this->getModelName()), ucfirst(camel_case(str_singular($model)))];
        sort($modelArray);
        return implode("_", $modelArray);
    }
    
    public function getPivotName($model)
    {
        $modelArray = [$this->getModelName(), ucfirst(camel_case(str_singular($model)))];
        sort($modelArray);
        return implode("", $modelArray);
    }

    public function getModelName()
    {
        return ucfirst($this->columnName);
    }

}

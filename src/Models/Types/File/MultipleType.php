<?php
namespace Prateekkarki\Laragen\Models\Types\File;
use Prateekkarki\Laragen\Models\Types\FileType;

class MultipleType extends FileType
{
    protected $hasPivot = true;

    public function getPivotSchema($modelName, $moduleName)
    {
        $schema = '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');";
        $schema .= '$table->string("filename", 192);';
        $schema .= '$table->timestamps();';
        return $schema;
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

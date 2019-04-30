<?php
namespace Prateekkarki\Laragen\Models\Types\Relational\Multiple;
use Prateekkarki\Laragen\Models\TypeResolver;
use Prateekkarki\Laragen\Models\Types\Relational\MultipleType;

class MultipleDataType extends MultipleType
{
    public function getPivotSchema($modelName, $moduleName)
    {
        $schema = PHP_EOL.$this->getTabs(3);
        $schema .= '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');".PHP_EOL.$this->getTabs(3);

        foreach ($this->getMultipleColumns() as $column => $optionString) {
            $option = new TypeResolver($column, $optionString);
            $schema .= $option->laragenType->getSchema();
            $schema .= ''.PHP_EOL.$this->getTabs(3);
        }
        return $schema;
    }

    public function getMultipleColumns()
    {
        return $this->optionString;
    }
}

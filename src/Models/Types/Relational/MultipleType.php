<?php
namespace Prateekkarki\Laragen\Models\Types\Relational;

use Prateekkarki\Laragen\Models\Types\RelationalType;
use Prateekkarki\Laragen\Models\DataOption;

class MultipleType extends RelationalType
{
    public function __construct($columnName, $optionString)
    {
        $this->columnName = $columnName;
        $this->optionString = $optionString;
        $this->multipleData = $optionString;
	}

    public function getPivotSchema($modelName, $moduleName)
    {
        $schema = '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');";

        foreach ($this->multipleData as $column => $optionString) {
            $option = new DataOption($column, $optionString);
            $schema .= $option->laragenType->getSchema();
            $schema .= ''.PHP_EOL.$this->getTabs(3);
        }
        return $schema;
    }

}

<?php
namespace Prateekkarki\Laragen\Models\Types;
use Prateekkarki\Laragen\Models\DataOption;

class MultipleType extends LaragenType
{
    public $isRelational = true;

    public function __construct($columnName, $optionString)
    {
        $this->columnName = $columnName;
        $this->optionString = $optionString;
        $this->multipleData = $optionString;
	}

    public function getTableSchema($modelName, $moduleName)
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

    public function getMigrationFile()
    {
        return "";
    }
}

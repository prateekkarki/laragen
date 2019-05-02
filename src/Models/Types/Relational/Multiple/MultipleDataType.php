<?php
namespace Prateekkarki\Laragen\Models\Types\Relational\Multiple;
use Prateekkarki\Laragen\Models\TypeResolver;
use Prateekkarki\Laragen\Models\Types\Relational\MultipleType;
use Illuminate\Support\Str;

class MultipleDataType extends MultipleType
{
    protected $hasModel = true;

    public function getPivotSchema()
    {
        $modelName = $this->getParentModelLowercase();
        $moduleName = $this->getParentModule();
        $schema = PHP_EOL.$this->getTabs(3);
        $schema .= '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');".PHP_EOL.$this->getTabs(3);

        foreach ($this->getMultipleColumns() as $column => $optionString) {
            $option = new TypeResolver($moduleName, $column, $optionString);
            $schema .= $option->laragenType->getSchema();
            $schema .= ''.PHP_EOL.$this->getTabs(3);
        }
        return $schema;
    }

    public function getPivot()
    {
        return $this->getParentModel() . ucfirst(Str::camel(Str::plural($this->columnName)));
    }
    
    public function getPivotTable()
    {
        return $this->getParentModelLowercase(). "_" . strtolower(Str::plural($this->columnName));
    }

    public function getMultipleColumns()
    {
        return $this->optionString;
    }

    public function getTypeColumns()
    {
        return array_merge([$this->getParentModelLowercase().'_id'], array_keys($this->optionString));
    }
}

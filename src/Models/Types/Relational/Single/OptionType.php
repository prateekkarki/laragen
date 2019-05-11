<?php
namespace Prateekkarki\Laragen\Models\Types\Relational\Single;

use Prateekkarki\Laragen\Models\Types\Relational\SingleType;
use Illuminate\Support\Str;

class OptionType extends SingleType
{
    protected $needsTableInit = true;
    protected $hasOptions = true;

    public function getSchema()
    {
        $schema = "";
        $schema .= "\$table->bigInteger('".$this->getColumn()."')->unsigned()->nullable();".PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$this->getColumn()."')->references('id')->on('".$this->getPivotTable()."')->onDelete('set null');".PHP_EOL;
        return $schema;
    }

    public function getDbData()
    {
        return explode(':',$this->optionArray[0]);
    }

    public function getPivotSchema()
    {
        $schema = '$table->string("title", 192);'.PHP_EOL.$this->getTabs(3);
        $schema .= '$table->timestamps();'.PHP_EOL.$this->getTabs(3);
        return $schema;
    }
    
    public function getRelatedModel()
    {
        return $this->getPivot();
    }
    
    public function getPivotTable()
    {
        return $this->getParentModelLowercase() . "_" . Str::plural($this->columnName);
    }

    public function getMigrationPivot()
    {
        return $this->getParentModel() . Str::plural($this->getChildModel());
    }

    public function getPivot()
    {
        return $this->getParentModel() . $this->getChildModel();
    }
    
    public function getTypeColumns()
    {
        return [$this->getParentModelLowercase().'_id', 'title'];
    }
}

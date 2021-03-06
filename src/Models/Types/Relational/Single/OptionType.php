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
        $schema .= "\$table->bigInteger('".$this->getForeignKey()."')->unsigned()->nullable();".PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$this->getForeignKey()."')->references('id')->on('".$this->getPivotTable()."')->onDelete('set null');".PHP_EOL;
        return $schema;
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
        return $this->getParentModelLowercase()."_".Str::plural($this->columnName);
    }

    public function getMigrationPivot()
    {
        return $this->getParentModel().Str::plural(ucfirst(Str::camel($this->columnName)));
    }

    public function getPivot()
    {
        return $this->getParentModel().ucfirst(Str::camel($this->columnName));
    }

    public function getTypeColumns()
    {
        return [$this->getParentModelLowercase().'_id', 'title'];
    }
}

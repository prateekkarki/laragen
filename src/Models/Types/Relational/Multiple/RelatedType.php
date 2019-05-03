<?php
namespace Prateekkarki\Laragen\Models\Types\Relational\Multiple;
use Illuminate\Support\Str;

use Prateekkarki\Laragen\Models\Types\Relational\MultipleType;

class RelatedType extends MultipleType
{
    protected $hasPivot = true;
    protected $stubs = [
        'foreignMethod' => 'common/Models/fragments/belongsToMany'
    ];

    public function getPivotSchema()
    {
        $modelName = $this->getParentModelLowercase();
        $moduleName = $this->getParentModule();
        $schema = PHP_EOL.$this->getTabs(3);
        $schema .= '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= '$table->foreign("'.$modelName.'_id")->references("id")->on("'.$moduleName.'")->onDelete("set null");'.PHP_EOL.$this->getTabs(3);

        $schema .= '$table->bigInteger("'. $this->getChildKey() .'")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= '$table->foreign("'.$this->getChildKey().'")->references("id")->on("'.$this->typeOption.'")->onDelete("set null");'.PHP_EOL;

        return $schema;
    }

    public function getChildKey()
    {
        return Str::singular($this->columnName) ."_id";
    }

    public function getChildModel()
    {
        return ucfirst(Str::camel(Str::singular($this->columnName)));
    }

    public function getPivotTable()
    {
        $modelArray = [$this->getParentModelLowercase(), strtolower(Str::singular($this->columnName))];
        sort($modelArray);
        return implode("_", $modelArray);
    }

    public function getMigrationPivot()
    {
        return $this->getParentModel() . $this->getChildModel();
    }

    public function getPivot()
    {
        $modelArray = [$this->getParentModel(), $this->getChildModel()];
        sort($modelArray);
        return implode("", $modelArray);
    }

    public function getTypeColumns()
    {
        return [$this->getParentModelLowercase().'_id', strtolower(Str::singular($this->typeOption)) . '_id'];
    }
}

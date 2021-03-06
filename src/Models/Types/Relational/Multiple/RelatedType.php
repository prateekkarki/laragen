<?php
namespace Prateekkarki\Laragen\Models\Types\Relational\Multiple;
use Prateekkarki\Laragen\Models\TypeResolver;
use Illuminate\Support\Str;

use Prateekkarki\Laragen\Models\Types\Relational\MultipleType;

class RelatedType extends MultipleType
{
    protected $hasPivot = true;
    protected $formType = 'related';
    protected $stubs = [
        'foreignMethod' => 'common/Models/fragments/belongsToMany'
    ];

    public function getPivotSchema()
    {
        $moduleName = $this->getParentModule();
        $schema = PHP_EOL.$this->getTabs(3);
        $schema .= '$table->bigInteger("'.$this->getParentKey().'")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= '$table->foreign("'.$this->getParentKey().'")->references("id")->on("'.$moduleName.'")->onDelete("set null");'.PHP_EOL.$this->getTabs(3);

        $schema .= '$table->bigInteger("'.$this->getChildKey().'")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= '$table->foreign("'.$this->getChildKey().'")->references("id")->on("'.$this->typeOption.'")->onDelete("set null");'.PHP_EOL;

        return $schema;
    }

    public function getParentKey()
    {
        return $this->getParentModelLowercase()."_id";
    }

    public function getRelatedModel()
    {
        return $this->getOptionModel();
    }

    public function getOptionModel()
    {
        return ucfirst(Str::camel(Str::singular($this->typeOption)));
    }

    public function getChildKey()
    {
        return Str::singular($this->columnName)."_id";
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
        $modelArray = [$this->getParentModel(), $this->getChildModel()];
        sort($modelArray);
        return implode("", $modelArray);
    }

    public function getPivot()
    {
        $modelArray = [$this->getParentModel(), $this->getChildModel()];
        sort($modelArray);
        return implode("", $modelArray);
    }

    public function getPivotColumns()
    {
        $columnModels = [];
        $columns = [
            $this->getParentModelLowercase() => 'parent:'.$this->getParentModule(),
            Str::singular($this->columnName) => 'parent:'.$this->typeOption,
        ];

        foreach ($columns as $column => $optionString) {
            $columnModels[$column] = TypeResolver::getType($this->getPivotTable(), $column, $optionString);
        }
        return $columnModels;
    }

    public function getTypeColumns()
    {
        return [$this->getParentModelLowercase().'_id', strtolower(Str::singular($this->typeOption)).'_id'];
    }
}

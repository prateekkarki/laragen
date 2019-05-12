<?php
namespace Prateekkarki\Laragen\Models\Types\Relational\Multiple;
use Prateekkarki\Laragen\Models\TypeResolver;
use Prateekkarki\Laragen\Models\Types\Relational\MultipleType;
use Illuminate\Support\Str;

class MultipleDataType extends MultipleType
{
    protected $hasModel = true;
    protected $isMultipleType = true;
    protected $formType = 'multiple';
    protected $stubs = [
        'modelMethod' => 'common/Models/fragments/belongsTo',
        'foreignMethod' => 'common/Models/fragments/hasMany'
    ];

    public function getPivotSchema()
    {
        $schema = PHP_EOL.$this->getTabs(3);
        foreach ($this->getPivotColumns() as $type) {
            $schema .= $type->getSchema().PHP_EOL.$this->getTabs(3);
        }
        $schema .= '$table->timestamps();'.PHP_EOL.$this->getTabs(3);
        return $schema;
    }

    public function getRelatedModel()
    {
        return $this->getPivot();
    }

    public function getPivot()
    {
        return $this->getParentModel() . $this->getChildModel();
    }

    public function getMigrationPivot()
    {
        return $this->getParentModel() . Str::plural($this->getChildModel());
    }

    public function getPivotTable()
    {
        return $this->getParentModelLowercase(). "_" . strtolower(Str::plural($this->columnName));
    }

    public function getPivotColumns()
    {
        $columns = [];
        foreach (array_merge([$this->getParentModelLowercase().'_id' => 'parent:'.$this->getParentModule()], $this->optionString )  as $column => $optionString) {
            $data = new TypeResolver($this->getPivotTable(), $column, $optionString);
            $columns[$column] = $data->getLaragenType();
        }
        return $columns;
    }

    public function getTypeColumns()
    {
        return array_merge([$this->getParentModelLowercase().'_id'], array_keys($this->optionString));
    }
}

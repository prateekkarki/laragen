<?php
namespace Prateekkarki\Laragen\Models\Types\Relational\Multiple;
use Illuminate\Support\Str;

use Prateekkarki\Laragen\Models\Types\Relational\MultipleType;

class RelatedType extends MultipleType
{
    protected $hasPivot = true;

    public function getPivotTable()
    {
        $modelArray = [$this->getParentModelLowercase(), strtolower(Str::singular($this->columnName))];
        sort($modelArray);
        return implode("_", $modelArray);
    }
    
    public function getPivot()
    {
        $modelArray = [$this->getParentModel(), ucfirst(Str::camel(Str::singular($this->columnName)))];
        sort($modelArray);
        return implode("", $modelArray);
    }
}

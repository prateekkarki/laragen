<?php

namespace Prateekkarki\Laragen\Models\Types\Relational;

use Prateekkarki\Laragen\Models\Types\RelationalType;

class SingleType extends RelationalType
{
    protected $dataType = 'integer';
    protected $hasSingleRelation = true;
    protected $size = false;
    protected $isRelational = false;
    protected $formType = 'parent';

    protected $stubs = [
        'modelMethod' => 'common/Models/fragments/hasOne',
        'foreignMethod' => 'common/Models/fragments/belongsTo'
    ];


    public function getValidationRule()
    {
        return 'exists:' . $this->getRelatedModule() . ',id';
    }

    public function hasSelfParent()
    {
        return ($this->typeOption == $this->getParentModule() || $this->typeOption == "self");
    }

    public function getColumnKey()
    {
        return $this->columnName . "_id";
    }

    public function getResourceTransformer()
    {
        return 'new ' . $this->getRelatedModel() . 'Resource($this->' . $this->getColumn() . ')';
    }
}

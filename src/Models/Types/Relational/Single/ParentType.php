<?php

namespace Prateekkarki\Laragen\Models\Types\Relational\Single;

use Illuminate\Support\Str;
use Prateekkarki\Laragen\Models\Types\Relational\SingleType;

class ParentType extends SingleType
{
    protected $isParent = true;

    public function getSchema()
    {
        $schema = "";
        $parentTable = $this->hasSelfParent() ? $this->getParentModule() : $this->typeOption;
        $schema .= "\$table->bigInteger('" . $this->getForeignKey() . "')->unsigned()->nullable();" . PHP_EOL . $this->getTabs(3);
        $schema .= "\$table->foreign('" . $this->getForeignKey() . "')->references('id')->on('$parentTable')->onDelete('set null');" . PHP_EOL;
        return $schema;
    }

    public function getPivot()
    {
        return ($this->typeOption == $this->getParentModule() || $this->typeOption == "self") ? $this->getParentModel() : ucfirst(Str::singular(Str::camel($this->typeOption)));
    }

    public function getRelatedModel()
    {
        return $this->getChildModel();
    }

    public function getChildModel()
    {
        return ($this->typeOption == $this->getParentModule() || $this->typeOption == "self") ? $this->getParentModel() : ucfirst(Str::singular(Str::camel($this->typeOption)));
    }

    public function getResourceTransformer()
    {
        return 'new ' . $this->getRelatedModel() . 'Resource($this->' . $this->getColumn() . ')';
    }
}

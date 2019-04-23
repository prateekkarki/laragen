<?php
namespace Prateekkarki\Laragen\Models\Types;

class IntegerType extends LaragenType
{
	protected $dataType = 'integer';

    public function getSchema()
    {
        $schema = '$table->'.$this->getDataType()."('{$this->getColumn()}')";
        $schema .= $this->getSize() ? "->length(" . $this->getSize() . ")" : "";
        $schema .= $this->isUnique() ? "->unique()" : "";
        $schema .= $this->isRequired() ? "" : "->nullable()";
        $schema .= ";";
        return $schema;
	}
}

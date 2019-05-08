<?php
namespace Prateekkarki\Laragen\Models\Types\General;
use Prateekkarki\Laragen\Models\Types\GeneralType;

class IntegerType extends GeneralType
{
    protected $dataType = 'integer';
	protected $formType = 'integer';
	protected $size = false;

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

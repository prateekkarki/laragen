<?php
namespace Prateekkarki\Laragen\Models\Types;

class BooleanType extends LaragenType
{
	protected $dataType = 'boolean';
	    
    public function getSchema()
    {
        $schema = '$table->'.$this->getDataType()."('{$this->getColumn()}');";
        return $schema;
	}
	
}

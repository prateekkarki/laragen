<?php
namespace Prateekkarki\Laragen\Models\Types\General;
use Prateekkarki\Laragen\Models\Types\GeneralType;

class BooleanType extends GeneralType
{
	protected $dataType = 'boolean';
	protected $formType = 'boolean';
	    
    public function getSchema()
    {
        $schema = '$table->'.$this->getDataType()."('{$this->getColumn()}');";
        return $schema;
	}
	
}

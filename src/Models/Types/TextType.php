<?php
namespace Prateekkarki\Laragen\Models\Types;

class TextType extends LaragenType
{
	protected $dataType = 'text';
	    
    public function getSchema()
    {
        $schema = '$table->'.$this->getDataType()."('{$this->getColumn()}')";
        $schema .= $this->isRequired() ? "" : "->nullable();";
        return $schema;
	}
	
}

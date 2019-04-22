<?php
namespace Prateekkarki\Laragen\Models\Types;

class LaragenType
{
	public $isRelational = false;
	protected $columnName;
	protected $optionString;
	
    public function __construct($columnName, $optionString)
    {
        $this->columnName = $columnName;
        $this->optionString = $optionString;

        $this->optionArray = explode('|', $optionString);
        $typePieces = array_shift($this->optionArray);
        $type = explode(':', $typePieces);
        $this->typeOption = is_array($type) && count($type) >= 2 ? $type[1] : false;
	}
	
    public function isRelational()
    {
        return $this->isRelational;
    }
    
    public function getSchema()
    {
        return '';
	}
	
    public function getTabs($number)
    {
        $schema = "";
        for ($i = 0; $i < $number; $i++) { 
            $schema .= "    ";
        }
        return $schema;
    }
}

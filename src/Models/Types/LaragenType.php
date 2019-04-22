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
	}
	
    public function isRelational()
    {
        return $this->isRelational;
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

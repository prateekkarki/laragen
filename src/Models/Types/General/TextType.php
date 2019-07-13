<?php
namespace Prateekkarki\Laragen\Models\Types\General;
use Prateekkarki\Laragen\Models\Types\GeneralType;

class TextType extends GeneralType
{
	protected $dataType = 'text';
    protected $formType = 'text';
	protected $validationRule = 'string';

    public function getFormOptions() {
        $options = "";
        $options .= $this->isRequired() ? 'required="required" ' : ''; 
        $options .='rows="'.$this->getTextRows().'" '; 
        return $options;
    }

    public function getTextRows() {
        if (!$this->size)
            return 4;
        
        return floor($this->getsize() / 120);
    }
}

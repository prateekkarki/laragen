<?php
namespace Prateekkarki\Laragen\Models\Types;

use Illuminate\Support\Str;
use Prateekkarki\Laragen\Models\TypeResolver;

 /**
  * @method integer getSize()
  */
abstract class LaragenType
{
    protected $uniqueFlag;
    protected $requiredFlag;
    protected $isDisplay;
	protected $dataType;
	protected $formType;
	protected $stubs = [];
	protected $size = false;
	protected $validationRule = null;
	protected $moduleName;
	protected $columnName;
	protected $optionString;
	
    public function __construct($moduleName, $columnName, $optionString)
    {
        $this->moduleName = $moduleName;
        $this->columnName = $columnName;
        $this->optionString = $optionString;

        $this->optionArray = is_string($optionString) ? explode('|', $optionString) : [];
        $typePieces = array_shift($this->optionArray);
        $type = explode(':', $typePieces);
        $this->typeOption = is_array($type) && count($type) >= 2 ? $type[1] : false;

        if(in_array(TypeResolver::COLUMN_UNIQUE, $this->optionArray)){
            $this->setUnique();
        }
        if(in_array(TypeResolver::COLUMN_REQUIRED, $this->optionArray)){
            $this->setRequired();
        }
        if(in_array("*", $this->optionArray)){
            $this->setIsDisplay();
        }
    }
    
    public function __call($method, $params) {
        $var = lcfirst(substr($method, 3));
        
        if (strncasecmp($method, "get", 3) === 0) {
            return property_exists($this, $var) ? $this->$var : "";
        }

        if (strncasecmp($method, "set", 3) === 0 && isset($params[0])) {
            $this->$var = $params[0];
        }

        return property_exists($this, $method) ? $this->$method : "";
   }
	
    public function isRelational()
    {
       return $this->relationalType;
    }

    public function getSchema()
    {
        $schema = '$table->'.$this->getDataType()."('{$this->getColumn()}'";
        $schema .= $this->getSize() ? ", {$this->getSize()})" : ")";
        $schema .= $this->isUnique() ? "->unique()" : "";
        $schema .= $this->isRequired() ? "" : "->nullable()";
        $schema .= ";";

        return $schema;
	}

    public function getValidationLine()
    {
        $validationSegments = [];
        $modelname = strtolower(Str::camel(Str::singular($this->moduleName)));

        $validationSegments[] = $this->isRequired() ? 'required' : 'nullable';
        $validationSegments[] = $this->getValidationRule() ?? $this->getDataType();
        $rules = implode('|', $validationSegments);

        if ($this->isUnique()) {
            $validationLine = '($this->'.$modelname.') ? \'';
            $validationLine .= $rules . '|unique:'.$this->moduleName.','.$this->getColumn().','.'\''.'.$this->'.$modelname.'->id : \'';
            $validationLine .= $rules . '|unique:'.$this->moduleName.'\'';
        } else{
            $validationLine = "'{$rules}'";
        }
        return $validationLine;
    }

    public function getFormOptions() {
        $options = "";
        $options .= $this->isRequired() ? 'required="required" ' : '';
        return $options;
    }
    
    
    public function getForeignKey()
    {
        return $this->columnName . "_id";
    }

    public function getRelatedModel()
    {
        return $this->getChildModel();
    }

    public function getRelatedModule()
    {
        return Str::snake(Str::plural($this->getRelatedModel()));
    }

    public function getRelatedModelLowercase()
    {
        return strtolower($this->getRelatedModel());
    }

    public function getChildModel()
    {
        return ucfirst(Str::camel(Str::singular($this->typeOption ?? $this->columnName )));
    }
    
    public function getParentModel()
    {
        return ucfirst(Str::camel(Str::singular($this->moduleName)));
    }

    public function getParentModule()
    {
        return $this->moduleName;
    }

    public function getParentModelLowercase()
    {
        return Str::singular($this->moduleName);
    }

    public function getStub($type)
    {
        return isset($this->stubs[$type]) ? $this->stubs[$type] : false;
    }


    public function getTextRows() {
        if (!$this->size)
            return 4;
        
        return floor($this->getsize() / 120);
    }
    public function isUnique() {
        return $this->uniqueFlag;
    }

    public function isRequired() {
        return $this->requiredFlag;
    }

    public function optionArray() {
        return $this->optionArray;
    }

    public function getDisplay()
    {
        return Str::title(str_replace("_", " ", $this->columnName));
    }

    public function getColumn()
    {
        return $this->columnName;
    }

    public function getColumnKey()
    {
        return $this->columnName;
    }
    
    public function getDataType() {
        return $this->dataType;
    }
    
    public function getValidationRule() {
        return $this->validationRule;
    }

    protected function setUnique($set = true) {
        $this->uniqueFlag = ($set === true) ? true : false;
    }

    protected function setRequired($set = true) {
        $this->requiredFlag = ($set === true) ? true : false;
    }

    protected function setIsDisplay($set = true) {
        $this->isDisplay = ($set === true) ? true : false;
    }

    protected function setSize($size = null) {
        $this->size = $size;
    }

    protected function setOptions($optionType, $optionParam) {
        switch ($optionType) {
            case 'max':
                $this->setSize($optionParam);
                break;
            
            default:
                $this->$optionType = $optionParam;
                break;
        }
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

<?php
namespace Prateekkarki\Laragen\Models\Types;

use Illuminate\Support\Str;
use Prateekkarki\Laragen\Models\TypeResolver;

abstract class LaragenType
{
	protected $relationalType = false;
    protected $hasPivot = false;
    protected $hasModel = false;
	protected $size = 192;
    protected $uniqueFlag = false;
    protected $requiredFlag = false;
	protected $dataType = 'string';
	protected $stubs = [];
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

        
        foreach ($this->optionArray as $option) {
            if ($option == TypeResolver::COLUMN_UNIQUE) {
                $this->setUnique();
                continue;
            }
            if ($option == TypeResolver::COLUMN_REQUIRED) {
                $this->setRequired();
                continue;
            }
            if (Str::contains($option, ':')) {
                $optionPieces = explode(':', $option);
                $this->setOptions($optionPieces[0], $optionPieces[1]);
            }
        }
    }
    
    function __call($method, $params) {
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
   
   public function hasPivot()
   {
       return $this->hasPivot;
   }
   
   public function hasModel()
   {
       return $this->hasModel;
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

    public function getFormOptions() {
        $options = "";
        $options .= $this->isRequired() ? 'required="required" ' : '';
        return $options;
    }
    
    public function getFilteredColumns($options = [], $columnsOnly = false)
    {
        $filteredTypes = [];
        $options = is_array($options) ? $options : [$options];
        foreach($this->getPivotColumns() as $type){
            foreach ($options as $option) {
                if($type->$option()){
                    $filteredTypes[] = $columnsOnly ? $type->getColumn() : $type;
                    break;
                }
            }
        }
        return $filteredTypes;
    }

    public function getRelatedModel()
    {
        return $this->getChildModel();
    }

    public function getChildModel()
    {
        return ucfirst(Str::camel(Str::singular($this->typeOption ?: $this->columnName )));
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

    public function getSize() {
        return $this->size;
    }

    public function getColumn()
    {
        return $this->columnName;
	}
    
    public function getDataType() {
        return $this->dataType;
    }

    protected function setUnique($set = true) {
        $this->uniqueFlag = ($set === true) ? true : false;
    }

    protected function setRequired($set = true) {
        $this->requiredFlag = ($set === true) ? true : false;
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

<?php
namespace Prateekkarki\Laragen\Models\Types;

use Illuminate\Support\Str;
use Prateekkarki\Laragen\Models\DataOption;

class LaragenType
{
	public $isRelational = false;
	protected $columnName;
	protected $size = 192;
	protected $optionString;
    protected $uniqueFlag = false;
    protected $requiredFlag = false;

	protected $dataType = 'string';
	
    public function __construct($columnName, $optionString)
    {
        $this->columnName = $columnName;
        $this->optionString = $optionString;

        $this->optionArray = explode('|', $optionString);
        $typePieces = array_shift($this->optionArray);
        $type = explode(':', $typePieces);
        $this->typeOption = is_array($type) && count($type) >= 2 ? $type[1] : false;

        
        foreach ($this->optionArray as $option) {
            if ($option == DataOption::COLUMN_UNIQUE) {
                $this->setUnique();
                continue;
            }
            if ($option == DataOption::COLUMN_REQUIRED) {
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

   }   
	
    public function isRelational()
    {
        return $this->isRelational;
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
    
    public function getPivotTableName($model= "")
    {
        $moduleArray = [str_singular($model), str_singular($this->columnName)];
        sort($moduleArray);
        return implode("_", $moduleArray);
    }
    
    public function getPivotName($model= "")
    {
        $moduleArray = [ucfirst(str_singular($model)), ucfirst(str_singular($this->columnName))];
        sort($moduleArray);
        return implode("", $moduleArray);
    }

    
    public function getPivotFile($model= "", $counter = 0)
    {
        $fileCounter = sprintf('%06d', (int) date('His') + $counter);
        $filenamePrefix = date('Y_m_d_').$fileCounter."_";
        $fileName = "create_".str_singular($this->getPivotTableName($model, $this->columnName))."_table.php";

        return $filenamePrefix.$fileName;
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

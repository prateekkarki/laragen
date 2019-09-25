<?php
namespace Prateekkarki\Laragen\Models\Types;

use Illuminate\Support\Str;
use Prateekkarki\Laragen\Models\TypeResolver;

/**
  * @method integer getSize()
  * @method array getPivotColumns()
  */
abstract class LaragenType
{
    protected $unique;
    protected $required;
    protected $isDisplay;
    protected $dataType;
    protected $formType;
    protected $stubs = [];
    protected $size = false;
    protected $validationRule = null;
    protected $moduleName;
    protected $columnName;
    protected $optionString;
    protected $optionArray;
    protected $typeOption;

    public function __construct($moduleName, $columnName, $optionString)
    {
        $this->moduleName = $moduleName;
        $this->columnName = $columnName;
        $this->optionString = $optionString;

        $this->optionArray = is_string($optionString) ? explode('|', $optionString) : [];
        $typePieces = array_shift($this->optionArray);
        $type = explode(':', $typePieces);
        $this->typeOption = is_array($type) && count($type) >= 2 ? $type[1] : false;

        if (in_array(TypeResolver::COLUMN_UNIQUE, $this->optionArray)) {
            $this->setUnique();
        }
        if (in_array(TypeResolver::COLUMN_REQUIRED, $this->optionArray)) {
            $this->setRequired();
        }
        if (in_array("*", $this->optionArray)) {
            $this->setIsDisplay();
        }
    }

    protected function __call($method, $params) {
        $var = lcfirst(substr($method, 3));

        if (strncasecmp($method, "get", 3) === 0) {
            return property_exists($this, $var) ? $this->$var : "";
        }

        if (strncasecmp($method, "set", 3) === 0 && isset($params[0])) {
            $this->$var = $params[0];
        }

        return property_exists($this, $method) ? $this->$method : "";
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
        return Str::plural(strtolower(Str::snake($this->getRelatedModel())));
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
        if (!$this->size) {
           return 4;
        }

        return floor($this->getsize() / 120);
    }

    public function isUnique() {
        return $this->unique;
    }

    public function isRequired() {
        return $this->required;
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

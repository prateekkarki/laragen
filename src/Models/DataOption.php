<?php

namespace Prateekkarki\Laragen\Models;

class DataOption
{
    const TYPE_PARENT = 'parent';

    const TYPE_RELATED = 'related';
    
    const COLUMN_UNIQUE = 'unique';

    protected $specialType;

    protected $uniqueFlag;

    protected $size;

    protected $column;

    /**
     * List of all types of data.
     *
     * @var array
     */
    public static $types = [
        'integer',
        'string',
        'image',
        'file',
        'boolean',
        'text',
        'date',
        'datetime'
    ];

    public static $specialTypes = [
        'parent',
        'related'
    ];

    /**
     * Array of data type options
     *
     * @var array
     */
    protected $optionArray;

    
    /**
     * Key to type conversion array.
     *
     * @var array
     */
    protected $keyToType = [
        'integer' =>'integer',
        'string' =>'string',
        'image' =>'string',
        'file' =>'string',
        'boolean' =>'boolean',
        'text' =>'text',
        'date' =>'date',
        'datetime' =>'datetime'
    ];

    public function __construct($columnName, $optionString)
    {
        $this->column = $columnName;
        $this->size = false;
        $this->optionArray = explode('|', $optionString);
        
        $typePieces = array_shift($this->optionArray);
        $type = explode(':', $typePieces);
        $this->dataType = is_array($type) ? $type[0] : $type;
        $this->typeOption = is_array($type)&&count($type)>=2 ? $type[1] : false;
    }

    public function getSchema()
    {
        if ($this->hasSpecialSchema()) {
            $schema = $this->processSpecialSchema();
        } else {
            foreach ($this->optionArray as $option) {
                if ($option == self::COLUMN_UNIQUE)         $this->hasUnique();
                if (is_numeric($option) && $option <= 2048) $this->hasSize((int) $option);
            }

            $schema = '$table->'.$this->getColumnType()."('{$this->column}'";
            $schema .= $this->hasSize() ? ", {$this->getSize()})" : ")";
            $schema .= $this->isUnique() ? "->unique()" : "";
            $schema .= ";";
        }
        return $schema;
    }

    protected function getColumnType() {
        return $this->keyToType[$this->dataType];
    }

    protected function getSize() {
        return $this->size;
    }
    
    protected function isUnique() {
        return $this->uniqueFlag;
    }

    protected function hasUnique($set = true) {
        $this->uniqueFlag = ($set === true) ? true : false;
    }

    protected function hasSize($size = null) {
        if ($size !== null) {
            $this->size = $size;
        }
        return $this->size;
    }

    public function getTabs($number)
    {
        $schema = "";
        for ($i = 0; $i < $number; $i++) { 
            $schema .= "    ";
        }
        return $schema;
    }

    protected function processSpecialSchema() {
        $specialMethod = 'process'.ucfirst($this->specialType);
        return $this->$specialMethod();
    }

    protected function processParent() {
        $schema = "";
        $parent = $this->typeOption;
        $schema .= "\$table->integer('".str_singular($parent)."_id')->unsigned()->nullable();";
        $schema .= PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".str_singular($parent)."_id')->references('id')->on('$parent')->onDelete('set null');";
        return $schema;
    }

    protected function processRelated() {
        return "";
    }

    protected function hasSpecialSchema() {
        if(in_array($this->dataType, self::$specialTypes)){
            $this->specialType = $this->dataType;
            return true;
        }
        return false;
    }
}

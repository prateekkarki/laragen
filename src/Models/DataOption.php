<?php

namespace Prateekkarki\Laragen\Models;

class DataOption
{
    const DELIMETER = ':';

    const TYPE_PARENT = 'parent';

    const TYPE_RELATED = 'related';
    
    const COLUMN_UNIQUE = 'unique';

    protected $specialType;

    protected $uniqueFlag;

    protected $size;

    protected $column;

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
        'int' =>'integer',
        'string' =>'string',
        'bool' =>'boolean',
        'text' =>'text',
        'date' =>'datetime',
        'datetime' =>'datetime'
    ];

    public function __construct($columnName, $optionString)
    {
        $this->column = $columnName;
        $this->size = false;
        $this->optionArray = explode(self::DELIMETER, $optionString);
    }

    public function getSchema()
    {
        if ($this->hasSpecialSchema()) {
            $schema = $this->processSpecialSchema();
        }else{
            foreach ($this->optionArray as $option) {
                if ($option == self::COLUMN_UNIQUE)         $this->hasUnique();
                if (is_numeric($option) && $option <= 2048) $this->hasSize((int)$option);
            }

            $schema = '$table->' . $this->getType() . "('{$this->column}'";
            $schema .= $this->hasSize() ? ", {$this->getSize()})" : ")";
            $schema .= $this->isUnique() ? "->unique()" : "";
            $schema .= ";";
        }
        return $schema;
    }

    protected function getType(){
        return $this->keyToType[array_shift($this->optionArray)];
    }

    protected function getSize(){
        return $this->size;
    }
    
    protected function isUnique(){
        return $this->uniqueFlag;
    }

    protected function hasUnique($set = true){
        $this->uniqueFlag = ($set === true) ? true : false;
    }

    protected function hasSize($size = null){
        if ($size !== null) {
            $this->size = $size;
        }
        return $this->size;
    }

    public function getTabs($number)
    {
        $schema = "";
        for ($i=0; $i < $number; $i++) { 
            $schema .= "    ";
        }
        return $schema;
    }

    protected function processSpecialSchema(){
        $specialMethod = 'process' . ucfirst($this->specialType);
        return $this->$specialMethod();
    }

    protected function processParent(){
        $schema = "";
        $parent = array_pop($this->optionArray);
        $schema .= "\$table->integer('" . str_singular($parent) . "_id')->unsigned()->nullable();";
        $schema .= PHP_EOL . $this->getTabs(3);
        $schema .= "\$table->foreign('" . str_singular($parent) . "_id')->references('id')->on('$parent')->onDelete('set null');";
        return $schema;
    }

    protected function processRelated(){
        return "";
    }

    protected function hasSpecialSchema(){
        if ($this->optionArray[0] == self::TYPE_PARENT) {
            array_shift($this->optionArray);
            $this->specialType = self::TYPE_PARENT;
            return true;
        }
        
        if ($this->optionArray[0] == self::TYPE_RELATED) {
            array_shift($this->optionArray);
            $this->specialType = self::TYPE_PARENT;
            return true;
        }
        return false;
    }
}

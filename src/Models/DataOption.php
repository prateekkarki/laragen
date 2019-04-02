<?php
namespace Prateekkarki\Laragen\Models;
use Illuminate\Support\Str;

class DataOption
{
    const TYPE_PARENT = 'parent';

    const TYPE_RELATED = 'related';
    
    const COLUMN_UNIQUE = 'unique';

    const COLUMN_REQUIRED = 'required';

    protected $specialType;

    protected $uniqueFlag;

    protected $requiredFlag;

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
        'boolean',
        'text',
        'date',
        'datetime'
    ];

    public static $fileTypes = [   
        'image',
        'file'     
    ];

    public static $specialTypes = [
        'parent',
        'related'
    ];

    public static $relatedMultiple = [
      
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
        'datetime' =>'datetime',
        'date' =>'date'
    ];

    public function __construct($columnName, $optionString)
    {
        $this->column = $columnName;
        $this->size = false;
        // dump($optionString);
        if(is_array($optionString) ){
            $this->dataType = 'multiple';
            $this->multipleOptions = [];
            foreach($optionString as $col => $multString){
                $this->multipleOptions[] = new Self($col, $multString);
            }
            // dd($this->multipleOptions[0]);
            $column = $this->multipleOptions[0]->column;
            // dd($column);
            // $option = ;

        }else{
            $this->optionArray = explode('|', $optionString);
            $typePieces = array_shift($this->optionArray);
            $type = explode(':', $typePieces);
            $this->dataType = is_array($type) ? $type[0] : $type;
            $this->typeOption = is_array($type)&&count($type)>=2 ? $type[1] : false;

            foreach ($this->optionArray as $option) {
                if ($option == self::COLUMN_UNIQUE) {
                    $this->setUnique();
                    continue;
                }
                if ($option == self::COLUMN_REQUIRED) {
                    $this->setRequired();
                    continue;
                }
                if(Str::contains($option, ':')){
                    $optionPieces = explode(':', $option);
                    $this->setOptions($optionPieces[0], $optionPieces[1]);
                }
            }
        }    
        
        
    }

    public function getSchema()
    {
        if ($this->dataType=='parent') {
            $schema = $this->processParent();
        } else if($this->dataType=='related'||$this->dataType=='multiple'){
            $schema = '';
        } else {
            $schema = '$table->'.$this->getColumnType()."('{$this->column}'";
            $schema .= $this->getSize() ? ", {$this->getSize()})" : ")";
            $schema .= $this->isUnique() ? "->unique()" : "";
            $schema .= $this->isRequired() ? "" : "->nullable()";
            $schema .= ";";
        }
        return $schema;
    }

    public function getKey() {
        return $this->column;
    }

    public function getDisplay() {
        return ucfirst(str_replace('_', ' ', $this->column));
    }

    public function getType() {
        $type = $this->dataType;
        if($type=='string'){
            $type = (!$this->getSize() || $this->getSize()<=128) ? $type : 'text';
        } 
        
        return $type;
    }

    public function getSize() {
        return $this->size;
    }

    public function getParentModule() {
        return (in_array($this->dataType, self::$specialTypes)) ? $this->typeOption : '';
    }

    public function getParentModel() {
        return (in_array($this->dataType, self::$specialTypes)) ? ucfirst(camel_case(str_singular($this->typeOption))) : '';
    }

    public function isRequired() {
        return $this->requiredFlag;
    }

    public function getFormOptions() {
        $options = "";
        $options .= $this->isRequired() ? 'required="required" ' :''; 
        $options .= $this->getType()=='text' ? 'rows="'.$this->getTextRows().'" ' :''; 
        return $options;
    }


    public function getTextRows() {
        if(!$this->size)
            return 4;
        
        return floor($this->getsize()/120);
    }

    public function getTabs($number)
    {
        $schema = "";
        for ($i = 0; $i < $number; $i++) {
            $schema .= "    ";
        }
        return $schema;
    }

    public function isUnique() {
        return $this->uniqueFlag;
    }
    
    protected function getColumnType() {
        return $this->keyToType[$this->dataType];
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

    protected function setUnique($set = true) {
        $this->uniqueFlag = ($set === true) ? true : false;
    }

    protected function setRequired($set = true) {
        $this->requiredFlag = ($set === true) ? true : false;
    }

    protected function setSize($size = null) {
        $this->size = $size;
    }

    public function optionArray(){
        return $this->optionArray;
    }

    protected function processParent() {
        $schema = "";
        $parent = $this->typeOption;
        $schema .= "\$table->bigInteger('".str_singular($parent)."_id')->unsigned()->nullable();";
        $schema .= PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".str_singular($parent)."_id')->references('id')->on('$parent')->onDelete('set null');";
        return $schema;
    }

}

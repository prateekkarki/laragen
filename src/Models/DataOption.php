<?php
namespace Prateekkarki\Laragen\Models;

use Prateekkarki\Laragen\Models\Types\StringType;
use Prateekkarki\Laragen\Models\Types\BooleanType;
use Prateekkarki\Laragen\Models\Types\IntegerType;
use Prateekkarki\Laragen\Models\Types\TextType;
use Prateekkarki\Laragen\Models\Types\DateTimeType;
use Prateekkarki\Laragen\Models\Types\DateType;
use Prateekkarki\Laragen\Models\Types\File\Single\ImageType;
use Prateekkarki\Laragen\Models\Types\File\Single\MixedFileType;
use Prateekkarki\Laragen\Models\Types\File\Multiple\AttachmentType;
use Prateekkarki\Laragen\Models\Types\File\Multiple\GalleryType;
use Prateekkarki\Laragen\Models\Types\Relational\Single\ParentType;
use Prateekkarki\Laragen\Models\Types\Relational\Single\OptionType;
use Prateekkarki\Laragen\Models\Types\Relational\Multiple\MultipleDataType;
use Prateekkarki\Laragen\Models\Types\Relational\Multiple\RelatedType;

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
        'gallery',
        'multiple',
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
        'datetime' =>'datetime',
        'date' =>'date'
    ];

    /**
     * Key to laragen type conversion array.
     *
     * @var array
     */
    protected $keyToLaragenType = [
        'string' => StringType::class,
        'boolean' => BooleanType::class,
        'integer' => IntegerType::class,
        'text' => TextType::class,
        'datetime' => DateTimeType::class,
        'date' => DateType::class,
        'image' => ImageType::class,
        'file' => MixedFileType::class,
        'gallery' => GalleryType::class,
        'attachments' => AttachmentType::class,
        'parent' => ParentType::class,
        'options' => OptionType::class,
        'related' => RelatedType::class,
        'multiple' => MultipleDataType::class
    ];

    public function __construct($columnName, $optionString)
    {
        $this->column = $columnName;
        $this->size = false;
        if (is_array($optionString)) {
            $this->dataType = 'multiple';
            $this->laragenType = new $this->keyToLaragenType[$this->dataType]($columnName, $optionString);
            $this->multipleOptions = [];
            foreach ($optionString as $col => $multString) {
                $this->multipleOptions[] = new Self($col, $multString);
            }
        } else {
            $this->optionArray = explode('|', $optionString);
            $typePieces = array_shift($this->optionArray);
            $type = explode(':', $typePieces);
            $this->dataType = is_array($type) ? $type[0] : $type;
            $this->laragenType = new $this->keyToLaragenType[$this->dataType]($columnName, $optionString);
            $this->typeOption = is_array($type) && count($type) >= 2 ? $type[1] : false;
        }
    }

    public function getKey() {
        return $this->column;
    }

    public function getDisplay() {
        return ucfirst(str_replace('_', ' ', $this->column));
    }

    public function getType() {
        $type = $this->dataType;
        if ($type == 'string') {
            $type = (!$this->getSize() || $this->getSize() <= 128) ? $type : 'text';
        } 
        
        return $type;
    }

    public function getSize() {
        return $this->size;
    }

    public function isRequired() {
        return $this->requiredFlag;
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
}

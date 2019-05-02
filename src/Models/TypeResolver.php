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

class TypeResolver
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

    public function __construct($moduleName, $columnName, $optionString)
    { 
        if (is_array($optionString)) {
            $this->dataType = 'multiple';
        } else {
            $this->optionArray = explode('|', $optionString);
            $typePieces = array_shift($this->optionArray);
            $type = explode(':', $typePieces);
            $this->dataType = is_array($type) ? $type[0] : $type;
        }
        $this->laragenType = new $this->keyToLaragenType[$this->dataType]($moduleName, $columnName, $optionString);
    }

    public function getLaragenType() {
        return $this->laragenType;
    }
}
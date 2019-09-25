<?php
namespace Prateekkarki\Laragen\Models;

use Prateekkarki\Laragen\Models\Types\General\StringType;
use Prateekkarki\Laragen\Models\Types\General\BooleanType;
use Prateekkarki\Laragen\Models\Types\General\IntegerType;
use Prateekkarki\Laragen\Models\Types\General\TextType;
use Prateekkarki\Laragen\Models\Types\General\DateTimeType;
use Prateekkarki\Laragen\Models\Types\General\DateType;
use Prateekkarki\Laragen\Models\Types\File\Single\ImageType;
use Prateekkarki\Laragen\Models\Types\File\Single\MixedFileType;
use Prateekkarki\Laragen\Models\Types\File\Multiple\AttachmentType;
use Prateekkarki\Laragen\Models\Types\File\Multiple\GalleryType;
use Prateekkarki\Laragen\Models\Types\Relational\Single\ParentType;
use Prateekkarki\Laragen\Models\Types\Relational\Single\OptionType;
use Prateekkarki\Laragen\Models\Types\Relational\Multiple\MultipleDataType;
use Prateekkarki\Laragen\Models\Types\Relational\Multiple\RelatedType;

/**
 * Contains static properties and methods usable in other classes
 */
class TypeResolver
{
    const TYPE_PARENT = 'parent';
    const TYPE_RELATED = 'related';
    const COLUMN_UNIQUE = 'unique';
    const COLUMN_REQUIRED = 'required';

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
     * Key to laragen type conversion array.
     *
     * @var array
     */
    protected static $keyToLaragenType = [
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

    /**
     * Finds the data type of of given column and
     * returns an implementation of \Prateekkarki\Laragen\Models\Types\LaragenType
     *
     * @param string $moduleName
     * @param string $columnName
     * @param string $optionString
     * @return \Prateekkarki\Laragen\Models\Types\LaragenType
     */
    public static function getType($moduleName, $columnName, $optionString)
    {
        // Find the datatype from the option string
        if (is_array($optionString)) {
            $dataType = 'multiple';
        } else {
            $optionArray = explode('|', $optionString);
            $typePieces = array_shift($optionArray);
            $types = explode(':', $typePieces);
            $dataType = $types[0];
        }

        // Create and return an instance of required type
        $class = self::$keyToLaragenType[$dataType];
        return new $class($moduleName, $columnName, $optionString);
    }
}

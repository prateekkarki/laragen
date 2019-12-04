<?php

namespace Prateekkarki\Laragen\Models\Types;

use Illuminate\Support\Str;
use Prateekkarki\Laragen\Models\TypeResolver;

/**
 * The LaragenType abstract class. This class cannot be instantiated. It's implementations are used to create new types.
 * Column instances are created from types that are implementations of this class.
 *
 * @method integer getSize()
 * @method integer getDataType()
 * @method array getPivotColumns()
 * @method void setSize()
 * @method void setIsRequired()
 * @method void setIsUnique()
 * @method void setIsDisplay()
 */
abstract class LaragenType
{
    /**
     * Denotes if the column is unique.
     *
     * @var bool
     */
    protected $isUnique;

    /**
     * Denotes if the column is required.
     *
     * @var bool
     */
    protected $isRequired;

    /**
     * Denotes if the column is used as title. Can be in case of select or checkboxes.
     *
     * @var bool
     */
    protected $isDisplay;

    /**
     * The data type of column used in the database.
     * Stores name of a column creation method of \Illuminate\Database\Schema\Blueprint
     *
     * @var string
     */
    protected $dataType;

    /**
     * The stub to be used in form generation of column.
     *
     * @var string
     */
    protected $formType;

    /**
     * Defines additional stubs to be used for
     * Defines stubs for relational types to be used in models generation
     *
     * @var array
     */
    protected $stubs = [];

    /**
     * The size of column in the database.
     *
     * @var int
     */
    protected $size = 0;

    /**
     * Validation rule for the column to be used in Request file.
     *
     * @var string|null
     */
    protected $validationRule = null;

    /**
     * Name of the module the column belongs to.
     *
     * @var string
     */
    protected $moduleName;

    /**
     * Name of the column.
     * e.g for 'short_description' => 'string|max:512', 'short_description' is the columnName.
     *
     * @var string
     */
    protected $columnName;

    /**
     * Special type assigned to the column e.g parent, related
     *
     * @var string|null
     */
    protected $typeOption;

    public function __construct($moduleName, $columnName, $optionString)
    {
        $this->moduleName = $moduleName;
        $this->columnName = $columnName;

        $optionArray = is_string($optionString) ? explode('|', $optionString) : [];
        $typePieces = array_shift($optionArray);
        $type = explode(':', $typePieces);
        $this->typeOption = is_array($type) && count($type) >= 2 ? $type[1] : null;

        if (in_array(TypeResolver::COLUMN_UNIQUE, $optionArray)) {
            $this->setsIsUnique(true);
        }
        if (in_array(TypeResolver::COLUMN_REQUIRED, $optionArray)) {
            $this->setIsRequired(true);
        }
        if (in_array(TypeResolver::COLUMN_DISPLAY, $optionArray)) {
            $this->setIsDisplay(true);
        }
    }

    public function __call($method, $params)
    {
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
        $schema = '$table->' . $this->getDataType() . "('{$this->getColumn()}'";
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
        foreach ($this->getPivotColumns() as $type) {
            foreach ($options as $option) {
                if ($type->$option()) {
                    $filteredTypes[] = $columnsOnly ? $type->getColumn() : $type;
                    break;
                }
            }
        }
        return $filteredTypes;
    }

    public function getFormOptions()
    {
        $options = "";
        $options .= $this->isRequired() ? 'required="required" ' : '';
        return $options;
    }

    public function getResourceTransformer()
    {
        return '$this->' . $this->getColumnKey();
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
        return ucfirst(Str::camel(Str::singular($this->typeOption ?? $this->columnName)));
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

    public function getTextRows()
    {
        if (!$this->size) {
            return 4;
        }

        return floor($this->getsize() / 120);
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

    protected function setOptions($optionType, $optionParam)
    {
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

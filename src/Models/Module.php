<?php
namespace Prateekkarki\Laragen\Models;
use Prateekkarki\Laragen\Models\TypeResolver;
use Illuminate\Support\Str;

class Module
{
    /**
     * The module name in plural snake case
     *
     * @var string
     */
    protected $moduleName;

    /**
     * Option to generate seo fields like 'seo_keywords', 'seo_descriptions', etc.
     *
     * @var boolean
     */
    protected $seoFields;

    /**
     * Option to generate generic fields like 'status' and 'sort'
     *
     * @var boolean
     */
    protected $genericFields;

    /**
     * Array of data to be seeded, this data is generated in LaragenSeeder,
     * Data is seeded when running laragen:seed
     *
     * @var array|null
     */
    protected $seedableData;

    /**
     * Array of multiple columns inside current module.
     * Contains data for child modules of current module
     *
     * @var array
     */
    protected $multipleData;

    /**
     * Array of all the columns in the module.
     * Contains array of different instances of \Prateekkarki\Laragen\Models\Types\LaragenType
     *
     * @var array
     */
    protected $columnsData;

    /**
     * Array of columns of module that are considered 'display columns',
     * first item of display column is used in Frontend as title,
     * other items are (to be) displayed in listing page of backend
     *
     * @var array
     */
    protected $displayColumns;

    public function __construct($moduleName, $moduleData)
    {
        $this->moduleName = $moduleName;
        $this->moduleData = $moduleData;

        $this->setAdditionalFields();
        $this->setSeedableData();
        $this->setColumnsData();
        $this->setDisplayColumns();
    }

    private function setAdditionalFields()
    {
        if(isset($this->moduleData['additional_fields'])){
            $this->seoFields = $this->moduleData['additional_fields']['seo'] ?? config('laragen.options.seo_fields');
            $this->genericFields = $this->moduleData['additional_fields']['generic'] ?? config('laragen.options.generic_fields');
            unset($this->moduleData['additional_fields']);
        }
    }

    private function setSeedableData()
    {
        if(isset($this->moduleData['data'])){
            $this->seedableData = $this->moduleData['data'] ?? null;
            unset($this->moduleData['data']);
        }
    }

    private function setColumnsData()
    {
        $this->columnsData = [];

        $moduleStructure = $this->moduleData['structure'] ?? (!empty($this->moduleData) ? $this->moduleData : ['title' => 'string|max:128']);

        $this->multipleData = array_filter($moduleStructure, function($elem) {
            return (is_array($elem)) ? true : false;
        });

        if ($this->genericFields) {
            $moduleStructure['sort'] = 'integer';
            $moduleStructure['status'] = 'boolean';
        }

        if ($this->seoFields) {
            $moduleStructure['seo_title'] = 'string|max:192';
            $moduleStructure['seo_keywords'] = 'string|max:256';
            $moduleStructure['seo_description'] = 'string|max:500';
        }
        foreach ($moduleStructure as $columnName => $columnOptions) {
            $column = TypeResolver::getType($this->moduleName, $columnName, $columnOptions);
            $this->columnsData[$columnName] = $column;
        }
    }

    private function setDisplayColumns()
    {
        $this->displayColumns = [];

        foreach ($this->columnsData as $column) {
            if ($column->isDisplay())
                $this->displayColumns[] = $column;
        }

        if (sizeof($this->displayColumns) == 0) {
            $this->displayColumns[] = array_values($this->columnsData)[0];
        }
    }

    public function getTabTitles()
    {
        $tabs = ['General'];
        if (sizeof($this->getFilteredColumns(['isParent', 'hasPivot']))) {
            $tabs[] = 'Relations';
        }
        if (sizeof($this->getFilteredColumns('hasFile'))) {
            $tabs[] = 'Attachments';
        }
        if (sizeof($this->getFilteredColumns('hasImage'))) {
            $tabs[] = 'Images';
        }
        if (sizeof($this->getFilteredColumns('isMultipleType'))) {
            foreach ($this->getFilteredColumns('isMultipleType') as $column) {
                $tabs[] = Str::plural($column->getChildModel());
            }
        }
        $tabs[] = 'Seo';
        return $tabs;
    }

    public function getTabs()
    {
        $tabs = [['general', 'hasOptions']];
        if (sizeof($this->getFilteredColumns(['isParent', 'hasPivot']))) {
            $tabs[] = ['isParent', 'hasPivot'];
        }
        if (sizeof($this->getFilteredColumns('hasFile'))) {
            $tabs[] = 'hasFile';
        }
        if (sizeof($this->getFilteredColumns('hasImage'))) {
            $tabs[] = 'hasImage';
        }
        if (sizeof($this->getFilteredColumns('isMultipleType'))) {
            foreach ($this->getFilteredColumns('isMultipleType') as $column) {
                $tabs[] = Str::plural($column->getChildModel());
            }
        }
        $tabs[] = 'Seo';
        return $tabs;
    }

    public function getColumnsData()
    {
        return $this->columnsData;
    }

    public function getDisplayColumns()
    {
        return $this->displayColumns;
    }

    public function getPivotalColumns()
    {
        $relativeTypes = [];
        foreach ($this->columnsData as $column) {
            if ($column->isRelational() && $column->hasPivot()) {
                $relativeTypes[] = $column;
            }
        }
        return $relativeTypes;
    }

    public function getFilteredColumns($options = [], $columnsOnly = false)
    {
        $filteredTypes = [];
        $options = is_array($options) ? $options : [$options];
        foreach ($this->columnsData as $column) {
            foreach ($options as $option) {
                if ($column->$option()) {
                    $filteredTypes[] = $columnsOnly ? $column->getColumn() : $column;
                    break;
                }
            }
        }
        return $filteredTypes;
    }

    public function getColumns($onlyNonRelational = false, $columnsOnly = false)
    {
        $columns = [];
        foreach ($this->columnsData as $column) {
            if ($onlyNonRelational && $column->isRelational()) {
                continue;
            }
            if ($columnsOnly) {
                $columns[] = $column->getColumnKey();
            } else {
                $columns[$column->getColumnKey()] = $column;
            }
        }
        return $columns;
    }

    public function getLastColumn()
    {
        $keyArray = array_keys($this->getColumns(true, true));
        $lastColumn = array_pop($keyArray);
        return $lastColumn;
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function getModuleDisplayName()
    {
        return Str::title(str_replace('_', '', $this->moduleName));
    }

    public function getModelName()
    {
        return ucfirst(Str::camel(Str::singular($this->moduleName)));
    }

    public function getModelNamePlural()
    {
        return ucfirst(Str::camel($this->moduleName));
    }

    public function getModelNameLowercase()
    {
        return Str::singular($this->moduleName);
    }
}

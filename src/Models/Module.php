<?php
namespace Prateekkarki\Laragen\Models;
use Prateekkarki\Laragen\Models\TypeResolver;
use Illuminate\Support\Str;

class Module
{
    protected $module;

    protected $data;

    protected $name;

    public function __construct($moduleName, $moduleData)
    {
        $this->module = (object) $moduleData;
        $this->laragenData = $moduleData;
        $this->data = array_filter($moduleData, function($elem) {
            return (is_array($elem)) ? false : true;
        });
        $this->multipleData = [];
        $this->multipleData[] = array_filter($moduleData, function($elem) {
            return (is_array($elem)) ? true : false;
        });

        $moduleData['sort'] = 'integer';
        $moduleData['status'] = 'boolean';

        $this->columnsData = [];
        $this->displayColumns = [];
        foreach ($moduleData as $column => $typeOptions) {
            $data = new TypeResolver($moduleName, $column, $typeOptions);
            $type = $data->getLaragenType();
            $this->columnsData[$column] = $type;
            if($type->isDisplay())
                $this->displayColumns[] = $type;
        }

        if(sizeof($this->displayColumns)==0){
            $this->displayColumns[] = array_values($this->columnsData)[0];
        }

        $this->name = $moduleName;
    }

    public function getTabTitles()
    {
        $tabs = ['General'];
        if (sizeof($this->getFilteredColumns('isParent'))) {
            $tabs[] = 'Relations';
        }
        if (sizeof($this->getFilteredColumns('hasFile'))) {
            $tabs[] = 'Attachments';
        }
        if (sizeof($this->getFilteredColumns('hasImage'))) {
            $tabs[] = 'Images';
        }
        if (sizeof($this->getFilteredColumns('hasModel'))) {
            foreach ($this->getFilteredColumns('hasModel') as $type) {
                $tabs[] = Str::plural($type->getChildModel());
            }
        }
        $tabs[] = 'Seo';
        return $tabs;
    }

    public function getTabs()
    {
        $tabs = ['general'];
        if (sizeof($this->getFilteredColumns('isParent'))) {
            $tabs[] = 'isParent';
        }
        if (sizeof($this->getFilteredColumns('hasFile'))) {
            $tabs[] = 'hasFile';
        }
        if (sizeof($this->getFilteredColumns('hasImage'))) {
            $tabs[] = 'hasImage';
        }
        if (sizeof($this->getFilteredColumns('hasModel'))) {
            foreach ($this->getFilteredColumns('hasModel') as $type) {
                $tabs[] = Str::plural($type->getChildModel());
            }
        }
        $tabs[] = 'Seo';
        return $tabs;
    }

    public function getDisplayColumns()
    {
        return $this->displayColumns;
    }

    public function getPivotalColumns()
    {
        $relativeTypes = [];
        foreach($this->columnsData as $type){
            if($type->isRelational()&&$type->hasPivot()){
                $relativeTypes[] = $type;
            }
        }
        return $relativeTypes;
    }

    public function getFilteredColumns($options = [], $columnsOnly = false)
    {
        $filteredTypes = [];
        $options = is_array($options) ? $options : [$options];
        foreach($this->columnsData as $type){
            foreach ($options as $option) {
                if($type->$option()){
                    $filteredTypes[] = $columnsOnly ? $type->getColumn() : $type;
                    break;
                }
            }
        }
        return $filteredTypes;
    }

    public function getColumns($onlyNonRelational = false, $columnsOnly = false)
    {
        $columns = [];
        foreach($this->columnsData as $type){
            if($onlyNonRelational && $type->isRelational()){
                continue;
            }
            if($columnsOnly){
                $columns[] = $type->getColumn(); 
            }else{
                $columns[$type->getColumn()] = $type;
            }
        }
        return $columns;
    }

    public function getName()
    {
        return $this->name;
    }

    public function hasPivotRelations()
    {
        $hasRelations = false;
        foreach($this->columnsData as $column => $type){
            if($type->isRelational()&&$type->hasPivot()){
                $hasRelations = true;
                break;
            }
        }
        return $hasRelations;
    }

    public function getMultipleColumns()
    {
        return $this->multipleData;
    }

    public function getData()
    {
        $this->data['sort'] = 'integer';
        $this->data['status'] = 'boolean';

        return $this->data;
    }

    public function getLastColumn()
    {
        $keyArray = array_keys($this->getColumns(true, true));
        $lastColumn = array_pop($keyArray);
        return $lastColumn;
    }

    public function getBackendColumnTitles()
    {
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $optionArray = explode('|', $optionString);
            if (in_array($optionArray[0], ['string', 'int']) && in_array($column, ['title', 'firstname', 'lastname', 'name'])) {
                $data[$column] = ucwords($column);
            }
        }
        return array_merge($data, ['updated_at' => 'Last Updated', 'status' => 'Status']);
    }

    public function getNativeColumns()
    {
        $data = [];
        foreach ($this->data as $column => $optionString) {
            if (is_array($optionString)) {
                continue;
            }
            $optionArray = explode('|', $optionString);
            if (in_array($optionArray[0], TypeResolver::$types)) {
                $data[] = $column;
            }
        }
        if ($this->getForeignColumns()) {
            foreach ($this->getForeignColumns() as $relation => $tablename) {
                $columnName = array_values($tablename)[0];
                $data[] = str_singular($columnName).'_id';
            }
        }
        return $data;
    }

    public function getNativeData()
    {
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $optionArray = explode('|', $optionString);
            if (in_array($optionArray[0], TypeResolver::$types)) {
                $data[] = [$column => $optionArray[0]];
            }
        }
        return $data;
    }

    public function getWritableColumns()
    {
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $optionArray = explode('|', $optionString);
            if (in_array($optionArray[0], TypeResolver::$types)) {
                $data[] = [$column => $optionArray[0]];
            }
        }
        return $data;
    }
    
    public function getModuleName()
    {
        return $this->name;
    }

    public function getModuleDisplayName()
    {
        return Str::title(str_replace('_', '', $this->name));
    }

    public function getModelName()
    {
        return ucfirst(Str::camel(str_singular($this->name)));
    }

    public function getModelNamePlural()
    {
        return ucfirst(Str::camel($this->name));
    }

    public function getModelNameLowercase()
    {
        return str_singular($this->name);
    }
}

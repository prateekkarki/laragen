<?php
namespace Prateekkarki\Laragen\Models;
use Prateekkarki\Laragen\Models\TypeResolver;
use Illuminate\Support\Str;

class Module
{
    protected $name;


    public function __construct($moduleName, $moduleData)
    {
        $this->name = $moduleName;

        $this->seoFields = $moduleData['additional_fields']['seo'] ?? config('laragen.options.seo_fields');
        $this->genericFields = $moduleData['additional_fields']['generic'] ?? config('laragen.options.generic_fields');
        unset($moduleData['additional_fields']);

        $this->seedableData = $moduleData['data'] ?? false;
        unset($moduleData['data']);

        $moduleStructure = $moduleData['structure'] ?? (!empty($moduleData) ? $moduleData : ['title' => 'string|max:128']);
        unset($moduleData['structure']);

        // $this->multipleData = [];
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

        $this->columnsData = [];
        $this->seedData = $moduleData['data'] ?? null;
        $this->displayColumns = [];
        foreach ($moduleStructure as $column => $typeOptions) {
            $data = new TypeResolver($this->name, $column, $typeOptions);
            $type = $data->getLaragenType();
            $this->columnsData[$column] = $type;
            if($type->isDisplay())
                $this->displayColumns[] = $type;
        }

        if(sizeof($this->displayColumns)==0){
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
            foreach ($this->getFilteredColumns('isMultipleType') as $type) {
                $tabs[] = Str::plural($type->getChildModel());
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
            foreach ($this->getFilteredColumns('isMultipleType') as $type) {
                $tabs[] = Str::plural($type->getChildModel());
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
                $columns[] = $type->getColumnKey(); 
            }else{
                $columns[$type->getColumnKey()] = $type;
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

    public function getLastColumn()
    {
        $keyArray = array_keys($this->getColumns(true, true));
        $lastColumn = array_pop($keyArray);
        return $lastColumn;
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
        return ucfirst(Str::camel(Str::singular($this->name)));
    }

    public function getModelNamePlural()
    {
        return ucfirst(Str::camel($this->name));
    }

    public function getModelNameLowercase()
    {
        return Str::singular($this->name);
    }
}

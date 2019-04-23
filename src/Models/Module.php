<?php
namespace Prateekkarki\Laragen\Models;
use Prateekkarki\Laragen\Models\DataOption;

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

        foreach($this->laragenData as $column => $typeOptions){
            $data = new DataOption($column, $typeOptions);
            if($data->laragenType->isRelational()){
                $this->relativeTypes[] = $data->laragenType;
            }
        }

        $this->name = $moduleName;
    }

    public function getName()
    {
        return $this->name;
    }


    public function hasRelations()
    {
        $hasRelations = false;
        foreach($this->laragenData as $column => $typeOptions){
            $data = new DataOption($column, $typeOptions);
            if($data->laragenType->isRelational()){
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

    public function getColumns($onlyNonRelational = false, $columnsOnly = false)
    {
        $columns = [];
        foreach($this->getData() as $column => $optionString){
            $data = new DataOption($column, $optionString);
            if($onlyNonRelational && $data->laragenType->isRelational()){
                continue;
            }
            if($columnsOnly){
                $columns[] = $column; 
            }else{
                $columns[$column] = $data->laragenType;
            }
        }
        return $columns;
    }

    public function getBackendColumnTitles()
    {
        $data = ['S.N.'];
        foreach ($this->data as $column => $optionString) {
            $optionArray = explode('|', $optionString);
            if (in_array($optionArray[0], ['string', 'int']) && in_array($column, ['title', 'firstname', 'lastname', 'name'])) {
                $data[] = ucwords($column);
            }
        }
        return array_merge($data, ['Last Updated', 'Status', 'Actions']);
    }

    public function getNativeColumns()
    {
        $data = [];
        foreach ($this->data as $column => $optionString) {
            if (is_array($optionString)) {
                continue;
            }
            $optionArray = explode('|', $optionString);
            if (in_array($optionArray[0], DataOption::$types)) {
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
            if (in_array($optionArray[0], DataOption::$types)) {
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
            if (in_array($optionArray[0], DataOption::$types)) {
                $data[] = [$column => $optionArray[0]];
            }
        }
        return $data;
    }

    public function getRelatedTypes($type = 'all')
    {
        if (is_array($type)) {
            $types = $type;
        } else {
            $types = ($type == "all") ? DataOption::$relatedMultiple : [$type];
        }
        
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $dataOption = new DataOption($column, $optionString);
            if (in_array($dataOption->getType(), $types)) {
                $data[] = $column;
            }
        }
        return $data;
    }

    public function getFileColumns($type = 'all')
    {
        if (is_array($type)) {
            $types = $type;
        } else {
            $types = ($type == "all") ? DataOption::$fileTypes : [$type];
        }
        
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $dataOption = new DataOption($column, $optionString);
            if (in_array($dataOption->getType(), $types)) {
                $data[] = $column;
            }
        }
        return $data;
    }

    public function getParentColumns()
    {
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $dataOption = new DataOption($column, $optionString);
            if ($dataOption->getType() == DataOption::TYPE_PARENT) {
                $data[] = $column;
            }
        }
        return $data;
    }

    public function getGalleries()
    {
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $dataOption = new DataOption($column, $optionString);
            if ($dataOption->getType() == 'gallery') {
                $data[] = $column;
            }
        }
        return $data;
    }

    public function getForeignColumns($type = 'all')
    {
        if (is_array($type)) {
            $types = $type;
        } else {
            $types = ($type == "all") ? DataOption::$specialTypes : [$type];
        }
        
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $dataOption = new DataOption($column, $optionString);
            if (in_array($dataOption->getType(), $types)) {
                $data[] = [$column => $dataOption->laragenType->getParentModule()];
            }
        }
        return $data;
    }

    public function getForeignData($type = 'all')
    {
        if (is_array($type)) {
            $types = $type;
        } else {
            $types = ($type == "all") ? DataOption::$specialTypes : [$type];
        }
        
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $dataOption = new DataOption($column, $optionString);
            if (in_array($dataOption->getType(), $types)) {
                $data[] = [
                    'columnName'   => $column,
                    'parentModule' => $dataOption->laragenType->getParentModule(),
                    'parentModel'  => $dataOption->laragenType->getParentModel()
                ];
            }
        }
        return $data;
    }

    public function getPivotName($related)
    {
        $modelArray = [$this->getModelName(), ucfirst(camel_case(str_singular($related)))];
        sort($modelArray);
        return implode("", $modelArray);
    }


    public function getModuleName()
    {
        return $this->name;
    }

    public function getModuleDisplayName()
    {
        return ucfirst(str_replace('_', '', $this->name));
    }

    public function getDisplayColumn()
    {
        foreach ($this->data as $column => $optionString) {
            $optionArray = explode('|', $optionString);
            if (in_array($optionArray[0], ['string', 'int']) && in_array($column, ['title', 'firstname', 'lastname', 'name'])) {
                return $column;
            }
        }
    }

    public function getModelName()
    {
        return ucfirst(camel_case(str_singular($this->name)));
    }

    public function getModelNamePlural()
    {
        return ucfirst(camel_case($this->name));
    }

    public function getModelNameLowercase()
    {
        return str_singular($this->name);
    }
}

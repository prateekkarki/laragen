<?php

namespace Prateekkarki\Laragen\Models;

class Module
{
    protected $module;

    protected $data;

    protected $images;

    protected $name;

    public function __construct($module)
    {
        $this->module = (object)$module;
        $this->data   = $this->module->data;
        $this->name   = $this->module->name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getData()
    {
        return $this->data;
    }
    
    public function getNativeColumns()
    {
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $optionArray = explode(':', $optionString);
            if (in_array($optionArray[0], ['string', 'int', 'text', 'bool', 'date'])) {
                $data[] = $column;
            }
        }
        return $data;
    }
    
    public function getNativeData()
    {
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $optionArray = explode(':', $optionString);
            if (in_array($optionArray[0], ['string', 'int', 'text', 'bool', 'date', 'datetime'])) {
                $data[] = [$column => $optionArray[0]];
            }
        }
        return $data;
    }

    public function getForeignColumns($type = 'all')
    {
        if(is_array($type))
            $types = $type;
        else
            $types = ($type == "all") ? ['parent', 'related'] : [$type];
        
        $data = [];
        foreach ($this->data as $column => $optionString) {
            $optionArray = explode(':', $optionString);
            if (in_array($optionArray[0], $types)) {
                $data[] = [$column => $optionArray[1]];
            }
        }
        return $data;
    }

    public function getModuleName()
    {
        return $this->name;
    }

    public function getModelName()
    {
        return ucfirst(camel_case(str_singular($this->name)));
    }

    public function getModelNamePlural()
    {
        return ucfirst(camel_case($this->name));
    }

    public function getModelNameSingularLowercase()
    {
        return str_singular($this->name);
    }
}

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

<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;


class BaseGenerator
{	
	protected $module;

    public function getModule()
    {
        return $this->module;
    }

    public function setModule(Module $module)
    {
        $this->module = $module;
    }

    public function getStub($type)
    {
        return file_get_contents(__DIR__ . "/../resources/stubs/" . $type . ".stub");
    }

    public function buildTemplate($stub, $variables)
    {
        return str_replace(array_keys($variables), array_values($variables), $this->getStub($stub));
    }

    public function getTabs($number)
    {
        $schema = "";
        for ($i=0; $i < $number; $i++) { 
            $schema .= "    ";
        }
        return $schema;
    }
}

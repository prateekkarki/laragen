<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;

class BaseGenerator
{	
	protected $module;

    public function __construct(Module $module)
    {
        $this->setModule($module);
    }

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

    public function getPath($path)
    {
        $dir = base_path($path);

        if(!is_dir($dir))
            mkdir($dir, 0755, true);

        return $dir;
    }

    public function moduleToModelName($moduleName)
    {
        return ucfirst(camel_case(str_singular($moduleName)));
    }

    public function buildTemplate($stub, $replacements)
    {
        return str_replace(array_keys($replacements), array_values($replacements), $this->getStub($stub));
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

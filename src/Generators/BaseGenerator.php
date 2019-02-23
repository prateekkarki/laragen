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
        return str_replace("\r", '', file_get_contents(__DIR__."/../resources/stubs/".$type.".stub"));
    }

    public function getPath($path)
    {
        $dir = base_path($path);

        if (!is_dir($dir))
            mkdir($dir, 0755, true);

        return $dir;
    }

    public function moduleToModelName($moduleName)
    {
        return ucfirst(camel_case(str_singular($moduleName)));
    }

    public function initializeFile($fullFilePath, $stub, $initializeWithText = false) {
        if(file_exists($fullFilePath)){
            unlink($fullFilePath);
        }
        $seederTemplate = ($initializeWithText===false) ? $this->buildTemplate($stub) : $initializeWithText;
        file_put_contents($fullFilePath, $seederTemplate);
        return $fullFilePath;
    }

    public function initializeFiles($fileMaps = []) {
        foreach ($fileMaps as $file => $stub) {
            $this->initializeFile($file, $stub);
        }
    }

    public function buildTemplate($stub, $replacements = [])
    {
        return str_replace(array_keys($replacements), array_values($replacements), $this->getStub($stub));
    }

    public function updateFile($file, $replacements)
    {
        return str_replace(array_keys($replacements), array_values($replacements), file_get_contents($file));
    }

    public function insertIntoFile($file_path, $insert_marker, $text, $after = true) {
        $contents = str_replace("\r",'', file_get_contents($file_path));
        $new_contents = ($after) ? str_replace($insert_marker, $insert_marker.$text, $contents) : str_replace($insert_marker, $text.$insert_marker, $contents); 
        return file_put_contents($file_path, $new_contents);
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

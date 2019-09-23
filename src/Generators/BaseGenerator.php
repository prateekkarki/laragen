<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;
use Prateekkarki\Laragen\Models\FileSystem;

class BaseGenerator
{	
    protected $module;
    protected $fileSystem;

    public function __construct(Module $module)
    {
        $this->setModule($module);
        $this->fileSystem = new FileSystem();
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
        return $this->sanitize(file_get_contents(realpath(__DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."resources".DIRECTORY_SEPARATOR."stubs").DIRECTORY_SEPARATOR.$type.".stub"));
    }

    public function sanitize($string)
    {
        return str_replace("\r", '', $string);
    }

    public function getPath($path)
    {
        $dir = base_path($path);

        if (!is_dir($dir)) {
            $this->fileSystem->mkdir($dir, 0755);
        }

        return $dir;
    }
    
    function deleteFiles($target) {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK );

            foreach( $files as $file ){
                deleteFiles( $file );      
            }

            rmdir( $target );
        } elseif(is_file($target)) {
            unlink( $target );  
        }
    }

    public function moduleToModelName($moduleName)
    {
        return ucfirst(Str::camel(Str::singular($moduleName)));
    }

    public function initializeFile($fullFilePath, $stub, $initializeWithText = false) {
        if (file_exists($fullFilePath)) {
            $this->fileSystem->remove($fullFilePath);
        }
        $seederTemplate = ($initializeWithText === false) ? $this->buildTemplate($stub) : $initializeWithText;

        $cleanFilePath = $this->getCleanPath($fullFilePath);
        $this->fileSystem->dumpFile($cleanFilePath, $seederTemplate);
        return $fullFilePath;
    }

    public function getCleanPath($file) {
        return realpath(dirname($file)).DIRECTORY_SEPARATOR.basename($file);
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
        $contents = str_replace("\r", '', file_get_contents($file_path));
        $new_contents = ($after) ? str_replace($insert_marker, $insert_marker.$text, $contents) : str_replace($insert_marker, $text.$insert_marker, $contents); 
        $this->fileSystem->dumpFile($file_path, $new_contents);
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

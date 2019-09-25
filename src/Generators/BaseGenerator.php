<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;
use Prateekkarki\Laragen\Models\FileSystem;

class BaseGenerator
{
    /**
     * Module instance to be generated
     *
     * @var \Prateekkarki\Laragen\Models\Module
     */
    protected $module;

    /**
     * Filesystem instance used to write files
     *
     * @var \Prateekkarki\Laragen\Models\FileSystem
     */
    protected $fileSystem;

    /**
     * File extension of the file to be generated
     * almost always equals "php", depending to the item being generated
     * kept to be able to generate html, txt or other similar file in the future
     *
     * @var string
     */
    protected $fileExtension = "php";

    /**
     * Suffix to be added to the end of filename, changes according to the generator
     * e.g. equals "Controller" on Controller generator to generate file PostController.php
     * e.g. equals ".blade" on view generator to generate file post.blade.php
     *
     * @var string
     */
    protected $fileSuffix = "";

    /**
     * Relative stub path for the child class
     * child classes usually just extend base class generated on laragen directory
     * child classes can be used to write updates that are not generated by laragen
     *
     * @var string
     */
    protected $childTemplate  = "backend/EmptyClass";

    /**
     * Option to check if generator requires a separate child class file to be generated
     *
     * @var boolean
     */
    protected $needsChildGeneration = true;

    /**
     * Create a new generator instance
     *
     * @param Module $module each generator requires a module to generate
     */
    public function __construct(Module $module)
    {
        $this->module = $module;
        $this->fileSystem = new FileSystem();
    }

    /**
     * Get current module being generated
     *
     * @return \Prateekkarki\Laragen\Models\Module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get stub content for the provided stubPath
     * allows the stub file from laragen/stubs directory on project base dir to override the one on package
     *
     * @param  string $stubPath relative path of the stub file to read from
     * @return string content read from the stub file
     */
    public function getStub($stubPath)
    {
        $customThemeStub = base_path('laragen/stubs/' . $stubPath.".stub");

        // Set stub file to the one from custom folder i.e. laragen/stubs if it exists, else use default from laragen package
        $stubFilePath = file_exists($customThemeStub) ?
            base_path('laragen/stubs/' . $stubPath.".stub") :
            realpath(__DIR__."/../stubs"). "/$stubPath.stub";

        return $this->getFileContents($stubFilePath);
    }

    /**
     * Sanitize the content for use in php
     * removes the carriage returns that comes from reading a file
     *
     * @param string  $content
     * @return string
     */
    public function sanitize($content)
    {
        return str_replace("\r", '', $content);
    }

    /**
     * Reads and returns the contents of a file as a string
     *
     * @param string $filePath absolute path of file to be read
     * @return string contents of the file
     */
    public function getFileContents($filePath){
        return $this->sanitize(file_get_contents($filePath));
    }

    /**
     * Get complete directory path by creating if not existing dir
     *
     * @param string $path
     * @return string
     */
    public function getPath($path)
    {
        $dir = base_path($path);

        if (!is_dir($dir)) {
            $this->fileSystem->mkdir($dir, 0755);
        }

        return $dir;
    }

    /**
     * Get file path for the base class of current generator
     *
     * @param  string|null $fileName is just the first part of filename without suffix or extension,
     *                               usually just null and the value is taken from model name of current module
     * @return string
     */
    public function getFilePath($fileName = null)
    {
        return $this->getPath($this->destination."/") . ($fileName ?? $this->module->getModelName()) . $this->fileSuffix . "." . $this->fileExtension;
    }

    /**
     * Get file path for the child class of current generator
     * very similar to getFilePath except this returns destination for child class
     * separate from getFilePath for better readability
     *
     * @param  string|null $fileName is same as in getFilePath above
     * @return string
     */
    public function getChildClassFilePath($fileName = null)
    {
        return $this->getPath($this->childDestination."/") . ($fileName ?? $this->module->getModelName()) . $this->fileSuffix . "." . $this->fileExtension;
    }

    /**
     * Generate file using the content and return the generated filename
     *
     * @param  string      $content
     * @param  string|null $file
     * @return string absolute path of generated file
     */
    public function generateFile($content, $filename = null)
    {
        $file = $this->getFilePath($filename);
        $this->fileSystem->dumpFile($file, $content);

        // Check if a child should be generated as in regular laravel file structure
        if($this->needsChildGeneration()){
            $childFile = $this->getChildClassFilePath($filename);
            if(!file_exists($childFile)){
                $childFileContent = $this->buildTemplate($this->childTemplate, [
                    '{{namespace}}'          => $this->childNamespace,
                    '{{className}}'          => $this->module->getModelName(),
                    '{{extendsClass}}'       => $this->namespace . '\\' . $this->module->getModelName()
                ]);
                $this->fileSystem->dumpFile($childFile, $childFileContent);
            }
        }

        return $file;
    }

    /**
     * Checks if current combination module and generator requires a child file to be generated
     *
     * ToDo: functionality to check for modules to be added
     *
     * @return boolean
     */
    public function needsChildGeneration(){
        return $this->needsChildGeneration;
    }

    /**
     * Delete files from target directory, used to remove previously generated files
     * Deletes all the files from target directory but not the directory itself
     *
     * @param string $target the directory to be cleared
     * @return void
     */
    public function deleteFiles($target) {
        if(is_dir($target)){
            $files = glob( $target . '*', GLOB_MARK );

            foreach( $files as $file ){
                $this->deleteFiles( $file );
            }

            rmdir( $target );
        } elseif(is_file($target)) {
            unlink( $target );
        }
    }

    /**
     * Used to initialize a new file at given path
     * rewrites if file already available
     *
     * @param string      $fullFilePath        absolute path of the file to write on
     * @param string      $stub                relative path of the stub file to read from
     * @param string|null $initializeContent   content to initialize the file with can be empty, just stub will be used in that case
     * @return void
     */
    public function initializeFile($fullFilePath, $stub, $initializeContent = null) {
        $content = $initializeContent ?? $this->buildTemplate($stub);
        $this->fileSystem->dumpFile($fullFilePath, $content);
    }

    /**
     * Iterates through provided filemaps and calls initializeFile()
     *
     * @param array $fileMaps
     * @return void
     */
    public function initializeFiles($fileMaps = []) {
        foreach ($fileMaps as $file => $stub) {
            $this->initializeFile($file, $stub);
        }
    }

    /**
     * Get contents to be written on generated file
     *
     * @param string $stub
     * @param array  $replacements
     * @return string
     */
    public function buildTemplate($stub, $replacements = [])
    {
        return str_replace(array_keys($replacements), array_values($replacements), $this->getStub($stub));
    }

    /**
     * Insert content into an existing file on the given position
     *
     * @param string  $file_path      absolute path of the file
     * @param string  $insert_marker  position of the file to be written on, this is a portion of content available on the file
     * @param string  $content        content to be written
     * @param boolean $after          option to write the content either after(true) or before(false) the insert marker
     * @return void
     */
    public function insertIntoFile($file_path, $insert_marker, $content, $after = true) {
        $contents = $this->getFileContents($file_path);
        $new_contents = ($after) ? str_replace($insert_marker, $insert_marker.$content, $contents) : str_replace($insert_marker, $content.$insert_marker, $contents);
        $this->fileSystem->dumpFile($file_path, $new_contents);
    }


    /**
     * Get string for the number of tabs
     * used to insert a certain number of tabs on a file
     *
     * @param int $number number of tabs to be generated
     * @return string
     */
    public function getTabs($number)
    {
        $str = "";
        for ($i = 0; $i < $number; $i++) {
            $str .= "    ";
        }
        return $str;
    }
}

<?php
namespace Prateekkarki\Laragen\Models;
use Prateekkarki\Laragen\Models\Module;

class LaragenOptions
{
    protected $modules;
    protected $options;

    private static $instance;

    private function __construct()
    {
        $this->options = config('laragen.options');

        $this->modules = [];
        foreach ($this->getValidatedModules() as $moduleName => $moduleData) {
            $this->modules = array_merge($this->modules, $this->getModulesRecursive($moduleName, $moduleData));
        }
    }
    
    private function getValidatedModules()
    {
        // Validate laragen.modules
        return config('laragen.modules');
    }

    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new LaragenOptions();
        }
        return self::$instance;
    }
    
    public function getOptions() {
        return $this->options;
    }
    
    public function getOption($option) {
        return $this->options[$option] ?? null;
    }
    
    public function getModules() {
        return $this->modules;
    }
    
    public function getModule($name) {
        return isset($this->modules[$name]) ? $this->modules[$name] : false;
    }
    
    protected function getModulesRecursive($moduleName, $moduleData) {
        $modules = [];
        $module = new Module($moduleName, $moduleData);
        $childColumns = $module->getFilteredColumns(['isMultipleType', 'hasMultipleFiles']);
        $modules[$moduleName] = $module;
        foreach ($childColumns as $childColumn) {
            $modules = array_merge($modules, $this->getModulesRecursive($childColumn->getPivotTable(), $childColumn->getLaragenColumns()));
        }
        return $modules;
    }
    
    public function getGenerators() {
        return $this->configToGenerators($this->options['items_to_generate']);
    }
    
    public function generatorExists($str) {
        foreach ($this->getGenerators() as $generator) {
            if (substr($generator, -strlen($str)) === $str) {
                return true;
            }
        }
        return false;
    }
    
    protected function configToGenerators($array) {
        $generators = [];
        foreach ($array as $ns => $items) {
            foreach ($items as $item) {
                $generators[] = "\\Prateekkarki\\Laragen\\Generators\\".$ns."\\".$item;
            }
        }
        return $generators;
    }
}

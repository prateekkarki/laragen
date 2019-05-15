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
        foreach (config('laragen.modules') as $moduleName => $moduleData) {
            $this->modules = array_merge($this->modules, $this->getModulesRecursive($moduleName, $moduleData));
        }
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
    
    public function getModules() {
        return $this->modules;
    }
    
    public function getModule($name) {
        return $this->modules[$name] ?: false;
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

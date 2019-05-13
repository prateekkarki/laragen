<?php
namespace Prateekkarki\Laragen\Models;
use Prateekkarki\Laragen\Models\TypeResolver;
use Illuminate\Support\Str;

class LaragenOptions
{
    protected $modules;

    protected $options;

    public function __construct()
    {
        $this->modules = config('laragen.modules');
        $this->options = config('laragen.options');
    }
    
    public function getOptions() {
        return $this->options;
    }
    
    public function getModules() {
        return $this->modules;
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

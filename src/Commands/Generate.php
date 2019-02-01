<?php

namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;
use Prateekkarki\Laragen\Models\Module;

class Generate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laragen:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate code for your project';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = config('laragen');
        foreach ($config['modules'] as $moduleName => $moduleArray) {
            $moduleArray['name'] = $moduleName;
            $module = new Module($moduleArray);

            // ToDo: to be loaded dynamically
            $itemsToGenerate = ['Migration', 'Controller', 'Model', 'View'];

            foreach ($itemsToGenerate as $item) {
                $generator = "\\Prateekkarki\\Laragen\\Generators\\{$item}";
                $itemGenerator = new $generator($module);
                $itemGenerator->generate();
            }
        }
    }
}

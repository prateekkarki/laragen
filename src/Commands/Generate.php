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
        $options = config('laragen')['options'];
        $modules = config('laragen')['modules'];

        $generatedFiles = [];

        $bar = $this->output->createProgressBar(count($modules));
        $bar->start();

        foreach ($modules as $moduleName => $moduleArray) {
            $moduleArray['name'] = $moduleName;
            $module = new Module($moduleArray);

            $itemsToGenerate = array_diff($options['generated_by_default'], $options['skip_generators']);
            
            foreach ($itemsToGenerate as $item) {
                $generator = "\\Prateekkarki\\Laragen\\Generators\\{$item}";
                $itemGenerator = new $generator($module);
                $returnedFiles = $itemGenerator->generate();
                
                if(!is_array($returnedFiles)) 
                    $generatedFiles[] = $returnedFiles;
                else
                    $generatedFiles = array_merge($generatedFiles, $returnedFiles);
            }
            
            $bar->advance();
        }

        $bar->finish();
        
        $this->line("\n");
        
        foreach ($generatedFiles as $file) {
            $this->info("Generated file: " . str_replace(base_path() . "\\", "", $file));
        }
    }
}

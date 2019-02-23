<?php

namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;
use Prateekkarki\Laragen\Models\Module;
use Prateekkarki\Laragen\Models\FileSystem;

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
     * Files to publish in development
     *
     * @var array
     */
    protected $filesToPublish = [
        'css' => 'public',
        'js' => 'public',
        'fonts' => 'public',
        'stubs/Views/layouts/backend/app.stub' => 'resources/views/backend/layouts/app.blade.php',
        'stubs/Views/layouts/backend/login.stub' => 'resources/views/backend/auth/login.blade.php',
        'stubs/Controllers/backend/LoginController.stub'     => 'app/Http/Controllers/Backend/Auth/LoginController.php',
        'stubs/Controllers/backend/DashboardController.stub' => 'app/Http/Controllers/Backend/DashboardController.php'
    ];

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
        $itemsToGenerate = array_diff($options['generated_by_default'], $options['skip_generators']);

        $bar = $this->output->createProgressBar(count($modules) * (count($itemsToGenerate) + count($this->filesToPublish)));
        $bar->setOverwrite(true);
        $bar->start();
        
        $fs = new FileSystem();

        foreach ($this->filesToPublish as $src => $dest) {
            $fs->clone($src, $dest);
        }

        foreach ($modules as $moduleName => $moduleArray) {
            $moduleArray['name'] = $moduleName;
            $module = new Module($moduleArray);
            
            foreach ($itemsToGenerate as $item) {
                $generator = "\\Prateekkarki\\Laragen\\Generators\\{$item}";
                $itemGenerator = new $generator($module);
                $returnedFiles = $itemGenerator->generate();
                
                if (!is_array($returnedFiles)) 
                    $generatedFiles[] = $returnedFiles;
                else
                    $generatedFiles = array_merge($generatedFiles, $returnedFiles);
                
                $bar->advance();
            }
        }

        $bar->finish();
        
        $this->line("\n");
        
        foreach ($generatedFiles as $file) {
            $this->info("Generated file: ".str_replace(base_path()."\\", "", $file));
        }
    }
}

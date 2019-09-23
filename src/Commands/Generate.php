<?php
namespace Prateekkarki\Laragen\Commands;
use Illuminate\Console\Command;
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


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $laragen = app('laragen');
        $modules = $laragen->getModules();
        $generators = $laragen->getGenerators();
        $this->filesToPublish = $laragen->getOption('files_to_publish') ?: [];
        $generatedFiles = [];

//         $this->line("
// ██▓    ▄▄▄       ██▀███   ▄▄▄        ▄████ ▓█████  ███▄    █ 
// ▓██▒   ▒████▄    ▓██ ▒ ██▒▒████▄     ██▒ ▀█▒▓█   ▀  ██ ▀█   █ 
// ▒██░   ▒██  ▀█▄  ▓██ ░▄█ ▒▒██  ▀█▄  ▒██░▄▄▄░▒███   ▓██  ▀█ ██▒
// ▒██░   ░██▄▄▄▄██ ▒██▀▀█▄  ░██▄▄▄▄██ ░▓█  ██▓▒▓█  ▄ ▓██▒  ▐▌██▒
// ░██████▒▓█   ▓██▒░██▓ ▒██▒ ▓█   ▓██▒░▒▓███▀▒░▒████▒▒██░   ▓██░
// ░ ▒░▓  ░▒▒   ▓▒█░░ ▒▓ ░▒▓░ ▒▒   ▓▒█░ ░▒   ▒ ░░ ▒░ ░░ ▒░   ▒ ▒ 
// ░ ░ ▒  ░ ▒   ▒▒ ░  ░▒ ░ ▒░  ▒   ▒▒ ░  ░   ░  ░ ░  ░░ ░░   ░ ▒░
//     ░ ░    ░   ▒     ░░   ░   ░   ▒   ░ ░   ░    ░      ░   ░ ░ 
//     ░  ░     ░  ░   ░           ░  ░      ░    ░  ░         ░ 
//                                                                 ");

//         $this->line("Generating code...");
//         $bar = $this->output->createProgressBar(count($modules) * (count($generators) + count($this->filesToPublish)));
//         $bar->setOverwrite(true);
//         $bar->start();
        $fs = new FileSystem();
        foreach ($this->filesToPublish as $src) {
            $fs->clone($src, '/');
        }

        foreach ($modules as $module) {
            
            foreach ($generators as $generator) {
                $itemGenerator = new $generator($module);
                $returnedFiles = $itemGenerator->generate();

                if (!is_array($returnedFiles)) 
                    $generatedFiles[] = $returnedFiles;
                else
                    $generatedFiles = array_merge($generatedFiles, $returnedFiles);
                
                // $bar->advance();
            }
        }
        // $bar->finish();
        
        // $this->line("\n");

        foreach ($generatedFiles as $key => $file) {
            \Log::info("Generated file: ".str_replace(base_path()."\\", "", $file));
        }
        // $this->info((isset($key) ? ++$key : 0)." files generated. Check log for details.");
        // $this->info("Cheers!!!");
    }
}

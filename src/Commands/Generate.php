<?php

namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;
use Prateekkarki\Laragen\Generators\Migration as MigrationGenerator;
use Prateekkarki\Laragen\Generators\Model as ModelGenerator;
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        MigrationGenerator $migrationGenerator,
        ModelGenerator $modelGenerator
    )
    {
        parent::__construct();
        $this->migrationGenerator = $migrationGenerator;
        $this->modelGenerator = $modelGenerator;
    }

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
            $this->migrationGenerator->generate($module);
            $this->modelGenerator->generate($module);
        }
    }
}

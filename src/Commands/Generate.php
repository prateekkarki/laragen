<?php

namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $config = config('laragen');
        foreach ($config['modules'] as $model => $module) {
            $this->migration(ucfirst(str_singular($model)), $module);
        }
    }

    protected function migration($model, $module)
    {
        $migrationTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePlural}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelTableData}}',
                '{{modelTimestampType}}'
            ],
            [
                $model,
                str_plural($model),
                strtolower(str_plural($model)),
                '',
                ''
            ],
            $this->getStub('Migration')
        );
        file_put_contents($this->laravel->databasePath(). "/migrations/" . date('Y_m_d_His') . "_create_" . strtolower(str_plural($model)) . "_table.php", $migrationTemplate);
    }

    protected function model($model)
    {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$model],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/Models/{$model}.php"), $modelTemplate);
    }

    protected function controller($model)
    {
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $model,
                strtolower(str_plural($model)),
                strtolower($model)
            ],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("/Http/Controllers/{$model}Controller.php"), $controllerTemplate);
    }

    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . "/../resources/stubs/" . $type . ".stub");
    }
}

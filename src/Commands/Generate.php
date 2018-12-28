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
     * Key to type conversion array.
     *
     * @var array
     */
    protected $keyToType = [
        'int' =>'integer',
        'string' =>'string',
        'bool' =>'boolean',
        'text' =>'text',
        'date' =>'datetime',
        'datetime' =>'datetime'
    ];

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
            $module['name'] = $model;
            $this->migration($module);
        }
    }

    protected function migration($module)
    {
        $migrationTemplate = $this->buildTemplate('Migration', [
            '{{modelName}}'                 => ucfirst(str_singular($module['name'])),
            '{{modelNamePlural}}'           => ucfirst($module['name']),
            '{{modelNamePluralLowerCase}}'  => $module['name'],
            '{{modelTableSchema}}'          => $this->getSchema($module)
        ]);
        file_put_contents($this->laravel->databasePath(). "/migrations/" . date('Y_m_d_His') . "_create_" . $module['name'] . "_table.php", $migrationTemplate);
    }

    protected function getSchema($module)
    {
        $schema = "";

        foreach ($module['data'] as $column => $optionString) {
            $options = explode(':', $optionString);
            if ($options[0]=='parent') {
                // Setup fk
                continue;
            }
            
            if ($options[0]=='related') {
                // Setup fk
                continue;
            }

            $schema .= "$table->" . $this->keyToType[$option[0]] . "('{$column}')";

            unset($option[0]);

            foreach ($options as $option) {
                if ($option == 'unique') {
                        $schema .= "->unique()"
                }
            }

            $schema .= ';'
        }
        
        return $schema;
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

    protected function buildTemplate($stub, $variables)
    {
        return str_replace(array_keys($variables), array_values($variables), $this->getStub($stub));
    }
}

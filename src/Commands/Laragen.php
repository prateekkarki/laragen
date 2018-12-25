<?php

namespace Prateekkarki\Laragen\Commands;

use Illuminate\Console\Command;

class Laragen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laragen:all';

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
        //
    }

    protected function getStub($type)
    {
        return file_get_contents(__DIR__ . "/../resources/stubs/$type.stub"));
    }

    protected function model($name)
    {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Model')
        );

        file_put_contents(app_path("/{$name}.php"), $modelTemplate);
    }

    protected function controller($name)
    {
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralLowerCase}}',
                '{{modelNameSingularLowerCase}}'
            ],
            [
                $name,
                strtolower(str_plural($name)),
                strtolower($name)
            ],
            $this->getStub('Controller')
        );

        file_put_contents(app_path("/Http/Controllers/{$name}Controller.php"), $controllerTemplate);
    }


}
